<?php

namespace MageSuite\ProductSymbols\Model;

class GroupsRepository implements \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
{
    const DEFAULT_STORE_ID = 0;
    /**
     * @var ResourceModel\Groups
     */
    protected $groupResource;
    /**
     * @var ResourceModel\Groups\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Groups\Processor\SaveFactory
     */
    protected $saveFactory;
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Groups $groupResource,
        \MageSuite\ProductSymbols\Model\ResourceModel\Groups\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\Groups\Processor\SaveFactory $saveFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->groupResource = $groupResource;
        $this->collectionFactory = $collectionFactory;
        $this->saveFactory = $saveFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function getById($id)
    {
        $tagsCollection = $this->collectionFactory->create();

        $tagsCollection->getSelect()
            ->where('entity_id =?', $id);

        if($tagsCollection->getSize()){
            return $tagsCollection->getFirstItem();
        }

        return null;
    }


    public function save(\MageSuite\ProductSymbols\Api\Data\GroupsInterface $group)
    {
        if($this->getByCode($group->getGroupCode())){
            throw new \Magento\Framework\Exception\AlreadyExistsException(new \Magento\Framework\Phrase('Group with the same code already exist.'));
        }
        $this->groupResource->save($group);
        $this->createGroupAttribute($group);

        return $group;
    }

    public function delete(\MageSuite\ProductSymbols\Api\Data\GroupsInterface $group)
    {
        $this->groupResource->delete($group);
        $this->removeGroupAttribute($group);
        return $this;
    }

    public function getByCode($groupCode)
    {
        $groupsCollection = $this->collectionFactory->create();

        $groupsCollection->getSelect()
            ->where('group_code =?', $groupCode);

        if($groupsCollection->getSize()){
            return $groupsCollection->getFirstItem();
        }

        return null;
    }

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupsInterface $group
     */
    protected function createGroupAttribute($group)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $group->getGroupCode(),
            [
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'type' => 'varchar',
                'unique' => false,
                'label' => $group->getGroupName(),
                'input' => 'multiselect',
                'source' => \MageSuite\ProductSymbols\Model\Source\SymbolsList::class,
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'group' => 'Symbol Groups',
                'required' => false,
                'sort_order' => 300,
                'user_defined' => 1,
                'searchable' => true,
                'filterable' => false,
                'filterable_in_search' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'comparable' => false,
                'visible' => 1,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 1,
                'is_visible_in_advanced_search' => 1,
                'is_comparable' => 1,
                'is_filterable' => 1,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 1,
                'used_for_sort_by' => 1,
                'system' => 0
            ]
        );
    }

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupsInterface $group
     */
    protected function removeGroupAttribute($group)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $group->getGroupCode());
    }
}