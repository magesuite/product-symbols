<?php

namespace MageSuite\ProductSymbols\Model\Source;

class SymbolList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const CACHE_TAG = 'symbol_options_store_%s';

    protected $collectionFactory;
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

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\SymbolFactory $model,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->symbolRepository = $symbolRepository;
        $this->storeManager = $storeManager;
        $this->model = $model;
        $this->groupRepository = $groupRepository;
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
        $group = $this->groupRepository->getByCode($attributeCode);

        $symbolsCollection = $this->collectionFactory->create();
        $symbolsCollection->setStoreId($storeId);

        $options = [];

        foreach ($symbolsCollection as $symbol) {
            $symbol = $this->symbolRepository->getById($symbol->getEntityId(), $storeId);
            $symbolGroups = explode(',', $symbol->getSymbolGroups());

            if (empty($group)) {
                return $options;
            }

            if (!in_array($group->getEntityId(), $symbolGroups)) {
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
