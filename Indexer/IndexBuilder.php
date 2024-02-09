<?php

namespace MageSuite\ProductSymbols\Indexer;

class IndexBuilder
{
    public const DEFAULT_BUNCH_SIZE = 100;
    protected ?array $symbolsCache = null;
    protected int $bunchSize;

    protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolsCollectionFactory;
    protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;
    protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel;
    protected \Magento\Framework\Indexer\CacheContext $cacheContext;
    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel,
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        \Psr\Log\LoggerInterface $logger,
        $bunchSize = self::DEFAULT_BUNCH_SIZE
    ) {
        $this->symbolsCollectionFactory = $symbolsCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->indexResourceModel = $indexResourceModel;
        $this->logger = $logger;
        $this->bunchSize = $bunchSize;
        $this->cacheContext = $cacheContext;
    }

    public function reindexList(array $ids): void
    {
        $symbols = $this->getSymbolsWithConditions();

        foreach ($this->getProducts($ids) as $products) {
            $this->buildIndex($products, $symbols);
        }
    }

    /**
     * @return \MageSuite\ProductSymbols\Model\Symbol[]
     */
    public function getSymbolsWithConditions(): array
    {
        if ($this->symbolsCache === null) {
            /** @var \MageSuite\ProductSymbols\Model\Symbol[] $symbols */
            $symbols = $this->symbolsCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(\MageSuite\ProductSymbols\Setup\Patch\Data\AddStatusAttribute::IS_ENABLED_ATTRIBUTE_CODE, 1) // phpcs:ignore
                ->load()
                ->getItems();

            /** @var \MageSuite\ProductSymbols\Model\Symbol $symbol */
            foreach ($symbols as $index => $symbol) {
                $symbol->setForceValidation(true);

                if ($symbol->hasConditions()) {
                    continue;
                }

                unset($symbols[$index]);
            }

            $this->symbolsCache = $symbols;
        }

        return $this->symbolsCache;
    }

    /**
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getProducts(array $ids)
    {
        foreach (array_chunk($ids, $this->bunchSize) as $idsChunk) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addIdFilter($idsChunk);

            yield $collection->getItems();
        }
    }

    protected function buildIndex(array $products, array $symbols): void
    {
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
                    'symbol_id' => $symbol->getId()
                ];
            }
        }

        try {
            $this->indexResourceModel->startTransaction();

            if (!empty($toDeleteProductIds)) {
                $this->indexResourceModel->deleteByProductId($toDeleteProductIds);
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
}
