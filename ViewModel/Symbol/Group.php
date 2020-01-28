<?php

namespace MageSuite\ProductSymbols\ViewModel\Symbol;

class Group extends \Magento\Framework\DataObject implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $product = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory
     */
    protected $symbolCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \MageSuite\ProductSymbols\Model\GroupToSymbolRelationRepository
     */
    protected $groupToSymbolRelationRepository;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Model\GroupToSymbolRelationRepository $groupToSymbolRelationRepository,
        array $data = []
    ) {
        parent::__construct($data);
        $this->registry = $registry;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->symbolCollectionFactory = $symbolCollectionFactory;
        $this->storeManager = $storeManager;
        $this->groupToSymbolRelationRepository = $groupToSymbolRelationRepository;
    }

    public function getGroupSymbols()
    {
        return $this->mapSymbolsToGroup();
    }

    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        return $this->product;
    }

    public function getGroupsToDisplay()
    {
        $groupCollection = $this->groupCollectionFactory->create();

        if (!empty($this->getIncludedGroups())) {
            $includedGroups = explode(',', $this->getIncludedGroups());

            $groupCollection->addFieldToFilter('group_code', ['in' => $includedGroups]);
        }

        if (!empty($this->getExcludedGroups())) {
            $excludedGroups = explode(',', $this->getExcludedGroups());

            $groupCollection->addFieldToFilter('group_code', ['nin' => $excludedGroups]);
        }

        return $groupCollection;
    }

    public function mapSymbolsToGroup()
    {
        $groups = $this->getGroupsToDisplay();
        $product = $this->getProduct();

        $groupIds = $groups->getColumnValues('entity_id');
        $symbolsCollection = $this->getSymbolsByGroups($groupIds);

        $groupFullData = [];

        foreach ($groups as $group) {
            $groupSymbols = $product->getData($group->getGroupCode());

            $groupSymbols = explode(',', $groupSymbols);

            $groupFullData[$group->getGroupCode()] = [
                'group_name' => $group->getGroupName()
            ];

            foreach ($symbolsCollection as $symbol) {
                if (!in_array($symbol->getEntityId(), $groupSymbols)) {
                    continue;
                }

                $groupFullData[$group->getGroupCode()]['symbols'][] = $symbol;
            }
        }

        return $groupFullData;
    }

    public function getSymbolsByGroups($groupIds)
    {
        $symbolIds = $this->groupToSymbolRelationRepository->getAllByGroupId($groupIds);

        $symbolsCollection = $this->symbolCollectionFactory->create();
        $symbolsCollection->setStoreId($this->storeManager->getStore()->getId());
        $symbolsCollection->addAttributeToSelect('*');
        $symbolsCollection->addFieldToFilter('entity_id', ['in' => $symbolIds]);

        return $symbolsCollection;
    }

    public function getGroupCssClass()
    {
        $groupCssClass = $this->getCssClassIdentifier();

        if (empty($groupCssClass)) {
            return '';
        }

        return $groupCssClass;
    }
}
