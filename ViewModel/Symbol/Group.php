<?php

namespace MageSuite\ProductSymbols\ViewModel\Symbol;

class Group extends \Magento\Framework\DataObject implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected \Magento\Framework\Registry $registry;

    protected \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory;

    protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolCollectionFactory;

    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    protected \MageSuite\ProductSymbols\Model\GroupToSymbolRelationRepository $groupToSymbolRelationRepository;

    protected ?\Magento\Catalog\Api\Data\ProductInterface $product = null;

    protected array $symbolGroups = [];

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
        $product = $this->getProduct();
        $groups = $this->getGroupsToDisplay();
        $groupIds = $groups->getColumnValues('entity_id');

        if (isset($this->symbolGroups[$product->getId()][implode('_', $groupIds)])) {
            return $this->symbolGroups[$product->getId()][implode('_', $groupIds)];
        }

        $result = [];
        $symbolsCollection = $this->getSymbolsByGroups($groupIds);

        foreach ($groups as $group) {
            $groupSymbols = $product->getData($group->getGroupCode());
            $groupSymbols = !empty($groupSymbols) ? explode(',', $groupSymbols) : [];

            foreach ($symbolsCollection as $symbol) {

                if (!$this->canDisplaySymbol($symbol, $product, $group, $groupSymbols)) {
                    continue;
                }

                $result[$group->getGroupCode()]['symbols'][] = $symbol;
            }
        }

        $this->symbolGroups[$product->getId()][implode('_', $groupIds)] = $result;
        return $result;
    }

    public function setProduct($product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): \Magento\Catalog\Api\Data\ProductInterface
    {
        if ($this->product) {
            return $this->product;
        }

        $this->product = $this->registry->registry('product');
        return $this->product;
    }

    public function getGroupsToDisplay(): \MageSuite\ProductSymbols\Model\ResourceModel\Group\Collection
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

    public function getSymbolsByGroups($groupIds): \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\Collection
    {
        $symbolIds = $this->groupToSymbolRelationRepository->getSymbolsByGroupId($groupIds);

        $symbolsCollection = $this->symbolCollectionFactory->create();
        $symbolsCollection->setStoreId($this->storeManager->getStore()->getId());
        $symbolsCollection->addAttributeToSelect('*');
        $symbolsCollection->addFieldToFilter('entity_id', ['in' => $symbolIds]);

        return $symbolsCollection;
    }

    public function getGroupCssClass(): string
    {
        $groupCssClass = $this->getCssClassIdentifier();

        if (empty($groupCssClass)) {
            return '';
        }

        return $groupCssClass;
    }

    protected function canDisplaySymbol($symbol, $product, $group, $groupSymbols): bool //phpcs:ignore
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
