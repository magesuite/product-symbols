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
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbols\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Model\SymbolsFactory $model,
        \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface $symbolsRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\CacheInterface $cache
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->symbolsRepository = $symbolsRepository;
        $this->storeManager = $storeManager;
        $this->model = $model;
        $this->cache = $cache;
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
        $cacheKey = sprintf(self::CACHE_TAG, $storeId);

        $options = json_decode($this->cache->load($cacheKey), true);

        if (is_array($options) and !empty($options)) {
            return $options;
        }

        $symbolsCollection = $this->collectionFactory->create();
        $symbolsCollection->setStoreId($storeId);

        $options = [];

        foreach ($symbolsCollection as $symbol) {
            $symbol = $this->symbolsRepository->getById($symbol->getEntityId(), $storeId);

            $options[] = [
                'label' => $symbol->getSymbolName(),
                'value' => $symbol->getEntityId()
            ];
        }

        $this->cache->save(json_encode($options), $cacheKey);

        return $options;
    }
}
