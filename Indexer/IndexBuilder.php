<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Indexer;

class IndexBuilder
{
    public const DEFAULT_BUNCH_SIZE = 500;

    protected array $symbolsList = [];
    protected ?array $attributeList = null;
    protected int $bunchSize;

    protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolsCollectionFactory;
    protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;
    protected \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory;
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel;
    protected \Magento\Framework\Indexer\CacheContext $cacheContext;
    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel,
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        \Psr\Log\LoggerInterface $logger,
        int $bunchSize = self::DEFAULT_BUNCH_SIZE
    ) {
        $this->symbolsCollectionFactory = $symbolsCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->storeManager = $storeManager;
        $this->indexResourceModel = $indexResourceModel;
        $this->cacheContext = $cacheContext;
        $this->logger = $logger;
        $this->bunchSize = $bunchSize;
    }

    public function reindexList(array $ids): void
    {
        foreach ($this->storeManager->getStores(false) as $store) {
            $storeId = (int)$store->getId();

            foreach ($this->getProducts($ids, $storeId) as $products) {
                $this->buildIndex($products, $storeId);
            }
        }
    }

    /**
     * @return \MageSuite\ProductSymbols\Model\Symbol[]
     */
    public function getSymbolsWithConditions(int $storeId): array
    {
        if (!array_key_exists($storeId, $this->symbolsList)) {
            $symbols = $this->symbolsCollectionFactory->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(\MageSuite\ProductSymbols\Setup\Patch\Data\AddStatusAttribute::IS_ENABLED_ATTRIBUTE_CODE, 1) // phpcs:ignore
                ->getItems();

            /** @var \MageSuite\ProductSymbols\Model\Symbol $symbol */
            foreach ($symbols as $index => $symbol) {
                $symbol->setForceValidation(true);

                if ($symbol->hasConditions()) {
                    continue;
                }

                unset($symbols[$index]);
            }

            $this->symbolsList[$storeId] = $symbols;
        }

        return $this->symbolsList[$storeId];
    }

    /**
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getProducts(array $ids, int $storeId): \Generator
    {
        foreach (array_chunk($ids, $this->bunchSize) as $idsChunk) {
            $collection = $this->productCollectionFactory->create();
            $collection->setStoreId($storeId);
            $collection->addAttributeToSelect($this->getAttributeList());
            $collection->addIdFilter($idsChunk);

            yield $collection->getItems();
        }
    }

    protected function buildIndex(array $products, int $storeId): void
    {
        $symbols = $this->getSymbolsWithConditions($storeId);
        $toDeleteProductIds = [];
        $toInsertSymbols = [];

        foreach ($products as $product) {
            $toDeleteProductIds[] = $product->getId();

            foreach ($symbols as $symbol) {
                if (!$symbol->validate($product)) {
                    continue;
                }

                $toInsertSymbols[] = [
                    'product_id' => $product->getId(),
                    'symbol_id' => $symbol->getId(),
                    'store_id' => $storeId
                ];
            }
        }

        try {
            $this->indexResourceModel->startTransaction();

            if (!empty($toDeleteProductIds)) {
                $this->indexResourceModel->deleteByProductId($toDeleteProductIds, $storeId);
                $this->cacheContext->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $toDeleteProductIds);
            }

            if (!empty($toInsertSymbols)) {
                $this->indexResourceModel->insert($toInsertSymbols);
            }

            $this->indexResourceModel->commit();
        } catch (\Throwable $e) {
            $this->logger->error('There has been an error when reindexing product symbols: ' . $e->getMessage());
            $this->indexResourceModel->rollBack();
        }
    }

    protected function getAttributeList(): array
    {
        if ($this->attributeList === null) {
            $this->attributeList = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_used_for_promo_rules', 1)
                ->getColumnValues('attribute_code');
        }

        return $this->attributeList;
    }
}
