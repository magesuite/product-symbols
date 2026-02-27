<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Indexer\Product;

class DataProvider
{
    protected const DEPLOYMENT_CONFIG_INDEXER_BATCHES = 'indexer/batch_size/';

    public function __construct(
        protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        protected \Magento\Framework\Event\ManagerInterface $eventManager,
        protected \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        protected \Magento\CatalogRule\Model\ResourceModel\Product\ConditionsToCollectionApplier $collectionApplier,
        protected int $batchSize = 1000
    ) {}

    public function getProducts(
        array $dimensions,
        \MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol,
        ?array $productIds = null
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection
    {
        $storeId = (int)$dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $collection = $this->productCollectionFactory->create();
        $symbol->getConditions()->collectValidatedAttributes($collection);
        $collection = $this->collectionApplier->applyConditionsToCollection($symbol->getConditions(), $collection);
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        if (!empty($productIds)) {
            $collection->addIdFilter($productIds);
        } else {
            $collection->setPageSize($this->getBatchSize());
        }

        $this->eventManager->dispatch(
            'product_symbols_index_collection_before_load',
            ['collection' => $collection]
        );

        return $collection;
    }

    public function getBatchSize(): int
    {
        $batchSize = (int)$this->deploymentConfig->get(
            self::DEPLOYMENT_CONFIG_INDEXER_BATCHES . \MageSuite\ProductSymbols\Model\Indexer\Product\Processor::INDEXER_ID
        );

        return $batchSize > 0 ? $batchSize : $this->batchSize;
    }
}
