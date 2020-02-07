<?php

namespace MageSuite\ProductSymbols\Model\Source;

class SymbolList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const CACHE_TAG = 'symbol_options_store_%s';

    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolFactory
     */
    protected $model;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var \MageSuite\ProductSymbols\Model\GroupToSymbolRelationRepository
     */
    protected $groupToSymbolRelationRepository;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\SymbolFactory $model,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        \MageSuite\ProductSymbols\Model\GroupToSymbolRelationRepository $groupToSymbolRelationRepository
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->model = $model;
        $this->symbolRepository = $symbolRepository;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->groupToSymbolRelationRepository = $groupToSymbolRelationRepository;
    }

    public function getAllOptions()
    {
        $storeId = $this->getAttribute()->getStoreId();

        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->getSymbolsFromStore($storeId);
    }

    private function getSymbolsFromStore($storeId)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $group = $this->groupRepository->getByCode($attributeCode);

        $symbolIds = $this->groupToSymbolRelationRepository->getSymbolsByGroupId($group->getEntityId());

        $symbolsCollection = $this->collectionFactory->create();
        $symbolsCollection->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $symbolIds]);

        $options = [];

        foreach ($symbolsCollection as $symbol) {
            $options[] = [
                'label' => $symbol->getSymbolName(),
                'value' => $symbol->getEntityId()
            ];
        }

        return $options;
    }
}
