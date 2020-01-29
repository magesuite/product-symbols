<?php

namespace MageSuite\ProductSymbols\Model;

class GroupToSymbolRelationRepository implements \MageSuite\ProductSymbols\Api\GroupToSymbolRelationRepositoryInterface
{
    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation\CollectionFactory
     */
    protected $groupToSymbolRelationCollectionFactory;
    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation
     */
    protected $groupToSymbolRelationResource;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation\CollectionFactory $groupToSymbolRelationCollectionFactory,
        \MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation $groupToSymbolRelationResource
    ) {
        $this->groupToSymbolRelationCollectionFactory = $groupToSymbolRelationCollectionFactory;
        $this->groupToSymbolRelationResource = $groupToSymbolRelationResource;
    }

    public function getSymbolsByGroupId($ids)
    {
        $collection = $this->groupToSymbolRelationCollectionFactory->create();

        if (is_array($ids)) {
            $collection->getSelect()
                ->where('group_id IN (?)', $ids);
        } else {
            $collection->getSelect()
                ->where('group_id =?', $ids);
        }

        if ($collection->getSize()) {
            return $collection->getColumnValues('symbol_id');
        }

        return null;
    }

    public function getGroupsBySymbolId($ids)
    {
        $collection = $this->groupToSymbolRelationCollectionFactory->create();

        if (is_array($ids)) {
            $collection->getSelect()
                ->where('symbol_id IN (?)', $ids);
        } else {
            $collection->getSelect()
                ->where('symbol_id =?', $ids);
        }

        if ($collection->getSize()) {
            return $collection->getColumnValues('group_id');
        }

        return null;
    }

    public function save(\MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface $relation)
    {
        try {
            $this->groupToSymbolRelationResource->save($relation);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save relation: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        return $relation;
    }

    public function deleteBySymbolId($id)
    {
        try {
            $connection = $this->groupToSymbolRelationResource->getConnection();
            $connection->delete(
                $connection->getTableName('product_group_to_symbol_relation'),
                [
                    $connection->prepareSqlCondition('symbol_id', ['eq' => $id])
                ]
            );

            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'Could not delete relation: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
    }
}
