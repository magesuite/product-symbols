<?php

namespace MageSuite\ProductSymbols\Model\Source;

class SymbolsList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const CACHE_TAG = 'symbol_options_store_%s';

    protected $collectionFactory;
    protected $model;

    protected $symbolsRepository;

    protected $storeManager;
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbols\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\SymbolsFactory $model,
        \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface $symbolsRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->symbolsRepository = $symbolsRepository;
        $this->storeManager = $storeManager;
        $this->model = $model;
        $this->groupsRepository = $groupsRepository;
    }

    /**
     * Options getter
     *
     * @return array
     */
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
        $group = $this->groupsRepository->getByCode($attributeCode);

        $symbolsCollection = $this->collectionFactory->create();
        $symbolsCollection->setStoreId($storeId);

        $options = [];

        foreach ($symbolsCollection as $symbol) {
            $symbol = $this->symbolsRepository->getById($symbol->getEntityId(), $storeId);
            $symbolGroups = explode(',', $symbol->getSymbolGroups());

            if(empty($group)){
                return $options;
            }

            if(!in_array($group->getEntityId(), $symbolGroups)){
                continue;
            }

            $options[] = [
                'label' => $symbol->getSymbolName(),
                'value' => $symbol->getEntityId()
            ];
        }

        return $options;
    }
}
