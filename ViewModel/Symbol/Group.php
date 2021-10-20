<?php

namespace MageSuite\ProductSymbols\ViewModel\Symbol;

class Group extends \Magento\Framework\DataObject implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
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

    protected $product = null;

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
        $groups = $this->getGroupsToDisplay();
        $product = $this->getProduct();

        $groupIds = $groups->getColumnValues('entity_id');
        $symbolsCollection = $this->getSymbolsByGroups($groupIds);

        $result = [];

        foreach ($groups as $group) {
            $groupSymbols = $product->getData($group->getGroupCode());
            $groupSymbols = explode(',', $groupSymbols);

            foreach ($symbolsCollection as $symbol) {

                if (!$this->canDisplaySymbol($symbol, $product, $group, $groupSymbols)) {
                    continue;
                }

                $result[$group->getGroupCode()]['symbols'][] = $symbol;
            }
        }

        return $result;
    }

    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct()
    {
        if ($this->product) {
            return $this->product;
        }

        $this->product = $this->registry->registry('product');
        return $this->product;
    }

    public function getGroupsToDisplay()
    {
        $groupCollection = $this->groupCollectionFactory->create();

        if (!empty($this->getIncludedGroups())) {
            $groupCollection->addFieldToFilter('group_code', ['in' => $this->getIncludedGroups()]);
        }

        if (!empty($this->getExcludedGroups())) {
            $groupCollection->addFieldToFilter('group_code', ['nin' => $this->getExcludedGroups()]);
        }

        return $groupCollection;
    }

    public function getSymbolsByGroups($groupIds)
    {
        $symbolIds = $this->groupToSymbolRelationRepository->getSymbolsByGroupId($groupIds);

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

    protected function canDisplaySymbol($symbol, $product, $group, $groupSymbols) //phpcs:ignore
    {
        if ($symbol->hasIsEnabled() && !$symbol->getIsEnabled()) {
            return false;
        }

        if (!$group->getIgnoreProductAssignment() && !in_array($symbol->getEntityId(), $groupSymbols)) {
            return false;
        }

        if (!$symbol->validate($product)) {
            return false;
        }

        return true;
    }
}
