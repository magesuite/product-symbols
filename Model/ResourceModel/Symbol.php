<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel;

class Symbol extends \Magento\Eav\Model\Entity\AbstractEntity
{
    protected $symbolAttributes = [
        'entity_id',
        'store_id',
        'symbol_name',
        'symbol_icon',
        'symbol_icon_url',
        'symbol_short_description',
        'symbol_groups',
    ];

    protected $storeId;
    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $productResourceAction;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupToSymbolRelationRepositoryInterface
     */
    protected $groupToSymbolRelationRepository;
    /**
     * @var \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterfaceFactory
     */
    protected $groupToSymbolRelation;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Api\GroupToSymbolRelationRepositoryInterface $groupToSymbolRelationRepository,
        \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterfaceFactory $groupToSymbolRelation,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        $data = [],
        \Magento\Eav\Model\Entity\Attribute\UniqueValidationInterface $uniqueValidator = null
    ) {
        parent::__construct($context, $data, $uniqueValidator);
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->productResourceAction = $productResourceAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->groupToSymbolRelationRepository = $groupToSymbolRelationRepository;
        $this->groupToSymbolRelation = $groupToSymbolRelation;
        $this->eavAttribute = $eavAttribute;
    }

    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\MageSuite\ProductSymbols\Model\Symbol::ENTITY);
        }
        return parent::getEntityType();
    }

    public function setDefaultStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function getDefaultStoreId()
    {
        if ($this->storeId == null) {
            return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }
        return $this->storeId;
    }

    public function updateAttribute($object, $attribute, $value, $storeId)
    {
        if ($attribute->getBackendType() != 'static') {
            $this->_updateAttributeForStore($object, $attribute, $value, $storeId);
        }
    }

    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $connection = $this->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $this->getLinkField();
        $select = $connection->select()
            ->from($table, 'value_id')
            ->where("$entityIdField = :entity_field_id")
            ->where('store_id = :store_id')
            ->where('attribute_id = :attribute_id');
        $bind = [
            'entity_field_id' => $object->getId(),
            'store_id' => $storeId,
            'attribute_id' => $attribute->getId(),
        ];
        $valueId = $connection->fetchOne($select, $bind);

        if ($valueId) {
            $bind = ['value' => $this->_prepareValueForSave($value, $attribute)];
            $where = ['value_id = ?' => (int) $valueId];

            $connection->update($table, $bind, $where);
        } else {
            $bind = [
                $entityIdField => (int) $object->getId(),
                'attribute_id' => (int) $attribute->getId(),
                'value' => $this->_prepareValueForSave($value, $attribute),
                'store_id' => (int) $storeId,
            ];

            $connection->insert($table, $bind);
        }

        return $this;
    }

    public function removeAttribute($symbol, $attributes)
    {
        foreach ($attributes as $attribute) {
            $attr = $this->getAttribute($attribute);
            $this->removeAttributeForStore($symbol, $attr, $symbol->getStoreId());
        }
    }

    protected function removeAttributeForStore($object, $attribute, $storeId)
    {
        $connection = $this->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $this->getLinkField();
        $select = $connection->select()
            ->from($table, 'value_id')
            ->where("$entityIdField = :entity_field_id")
            ->where('store_id = :store_id')
            ->where('attribute_id = :attribute_id');
        $bind = [
            'entity_field_id' => $object->getId(),
            'store_id' => $storeId,
            'attribute_id' => $attribute->getId(),
        ];
        $valueId = $connection->fetchOne($select, $bind);

        $where = ['value_id = ?' => (int) $valueId];
        $connection->delete($table, $where);

        return $this;
    }

    public function getAttributeRawValue($entityId, $attribute, $store)
    {
        $attribute = $this->getAttribute($attribute);
        $connection = $this->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $this->getLinkField();
        $select = $connection->select()
            ->from($table, 'value')
            ->where($entityIdField.' = ?', $entityId)
            ->where('store_id = ?', $store)
            ->where('attribute_id = ?', $attribute->getId());
        $result = $connection->fetchOne($select);

        return $result;
    }

    public function save(\Magento\Framework\Model\AbstractModel $symbol)
    {
        $currentTime = date('Y-m-d H:i:s');
        $symbol->setUpdatedAt($currentTime);

        if ($symbol->getIsNew()) {
            $symbol->setCreatedAt($currentTime);
            return parent::save($symbol);
        }

        $attributesToRemove = $this->symbolAttributes;
        foreach ($symbol->getData() as $key => $value) {
            $attr = $this->getAttribute($key);
            $attributeIndex = array_search($key, $attributesToRemove);
            if (false !== $attributeIndex) {
                unset($attributesToRemove[$attributeIndex]);
            }

            if (!$attr) {
                continue;
            }

            if ($key == 'symbol_groups') {
                $value = implode(',', $value);
                $this->updateGroupToSymbolRelation($symbol);
            }

            $this->updateAttribute($symbol, $attr, $value, $symbol->getStoreId());
        }
        $this->removeAttribute($symbol, $attributesToRemove);

        return $this;
    }

    protected function _afterSave(\Magento\Framework\DataObject $object)
    {
        $this->updateGroupToSymbolRelation($object);

        return parent::_afterSave($object);
    }

    protected function _afterDelete(\Magento\Framework\DataObject $object)
    {
        $this->removeSymbolIdFromProductSymbolAttributeValues($object->getEntityId());

        $this->groupToSymbolRelationRepository->deleteBySymbolId($object->getEntityId());

        return parent::_afterDelete($object);
    }

    protected function removeSymbolIdFromProductSymbolAttributeValues($symbolId)
    {
        $groupIds = $this->groupToSymbolRelationRepository->getGroupsBySymbolId($symbolId);
        $groupCollection = $this->groupCollectionFactory->create();
        $groupCollection->addFieldToFilter('entity_id', ['in' => $groupIds]);

        $groupAttributeIds = [];
        foreach ($groupCollection as $group) {
            $attributeId = $this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $group->getGroupCode());
            $groupAttributeIds[] = $attributeId;
        }

        $connection = $this->getConnection();

        $replaceCondition = sprintf("TRIM(BOTH ',' FROM REPLACE(REPLACE(CONCAT(',',REPLACE(value, ',', ',,'), ','),',%s,', ''), ',,', ','))", $symbolId);

        $connection->update(
            $this->getConnection()->getTableName('catalog_product_entity_varchar'),
            [
                'value' => new \Zend_Db_Expr($replaceCondition)
            ],
            [
                $connection->prepareSqlCondition('value', ['finset' => $symbolId]),
                $connection->prepareSqlCondition('attribute_id', ['in' => $groupAttributeIds])
            ]
        );
    }

    protected function _getLoadAttributesSelect($object, $table)
    {
        if ($this->storeManager->hasSingleStore()) {
            $storeId = (int) $this->storeManager->getStore(true)->getId();
        } else {
            $storeId = (int) $object->getStoreId();
        }

        $setId = $object->getAttributeSetId();
        $storeIds = [$this->getDefaultStoreId()];
        if ($storeId != $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['attr_table' => $table], [])
            ->where("attr_table.{$this->getLinkField()} = ?", $object->getData($this->getLinkField()))
            ->where('attr_table.store_id IN (?)', $storeIds);

        if ($setId) {
            $select->join(
                ['set_table' => $this->getTable('eav_entity_attribute')],
                $this->getConnection()->quoteInto(
                    'attr_table.attribute_id = set_table.attribute_id' . ' AND set_table.attribute_set_id = ?',
                    $setId
                ),
                []
            );
        }
        return $select;
    }

    protected function _prepareLoadSelect(array $selects)
    {
        $select = parent::_prepareLoadSelect($selects);
        $select->order('store_id');
        return $select;
    }

    protected function updateGroupToSymbolRelation($symbol)
    {
        $this->groupToSymbolRelationRepository->deleteBySymbolId($symbol->getEntityId());
        $symbolGroups = $symbol->getSymbolGroups();

        if (!is_array($symbolGroups)) {
            $symbolGroups = explode(',', $symbol->getSymbolGroups());
        }

        foreach ($symbolGroups as $symbolGroup) {
            $relation = $this->groupToSymbolRelation->create();
            $relation->setGroupId($symbolGroup);
            $relation->setSymbolId($symbol->getEntityId());

            $this->groupToSymbolRelationRepository->save($relation);
        }
    }
}
