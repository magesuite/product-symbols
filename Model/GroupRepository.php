<?php

namespace MageSuite\ProductSymbols\Model;

class GroupRepository implements \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
{
    /**
     * @var ResourceModel\Group
     */
    protected $groupResource;

    /**
     * @var ResourceModel\Group\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Group\Processor\SaveFactory
     */
    protected $saveFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Group $groupResource,
        \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\Group\Processor\SaveFactory $saveFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->groupResource = $groupResource;
        $this->collectionFactory = $collectionFactory;
        $this->saveFactory = $saveFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function getById($id)
    {
        $groupCollection = $this->collectionFactory->create();

        $groupCollection->getSelect()
            ->where('entity_id =?', $id);

        if ($groupCollection->getSize()) {
            return $groupCollection->getFirstItem();
        }

        return null;
    }

    public function save(\MageSuite\ProductSymbols\Api\Data\GroupInterface $group)
    {
        if (!$group->getEntityId() && $this->getByCode($group->getGroupCode())) {
            throw new \Magento\Framework\Exception\AlreadyExistsException(new \Magento\Framework\Phrase('Group with the same code already exist.'));
        }

        $this->createGroupAttribute($group);
        $this->groupResource->save($group);

        return $group;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria = null)
    {
        $collection = $this->collectionFactory->create();

        if ($criteria === null) {
            $criteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($criteria, $collection);
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(\MageSuite\ProductSymbols\Api\Data\GroupInterface $group)
    {
        $this->groupResource->delete($group);
        $this->removeGroupAttribute($group);
        return $this;
    }

    public function getByCode($groupCode)
    {
        $groupCollection = $this->collectionFactory->create();

        $groupCollection->getSelect()
            ->where('group_code =?', $groupCode);

        if ($groupCollection->getSize()) {
            return $groupCollection->getFirstItem();
        }

        return null;
    }

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
                'source' => \MageSuite\ProductSymbols\Model\Source\SymbolList::class,
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

    protected function removeGroupAttribute($group)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $group->getGroupCode());
    }
}
