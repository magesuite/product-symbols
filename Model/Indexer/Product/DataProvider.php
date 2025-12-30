<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Indexer\Product;

class DataProvider
{
    protected const DEPLOYMENT_CONFIG_INDEXER_BATCHES = 'indexer/batch_size/';

    public function __construct(
        protected \Magento\Catalog\Model\Config $catalogConfig,
        protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        protected \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        protected \Magento\Framework\Event\ManagerInterface $eventManager,
        protected \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        protected int $batchSize = 1000
    ) {}

    public function getProducts(
        array $dimensions,
        ?array $productIds = null,
        int $lastProductId = 0
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection
    {
        $storeId = (int)$dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $collection = $this->productCollectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToSelect($this->getAttributeList());

        if (!empty($productIds)) {
            $collection->addIdFilter($productIds);
        } else {
            $collection->setPageSize($this->getBatchSize());
        }

        if ($lastProductId > 0) {
            $collection->addFieldToFilter('entity_id', ['gt' => $lastProductId]);
        }

        $this->eventManager->dispatch(
            'product_symbols_index_collection_before_load',
            ['collection' => $collection]
        );

        return $collection;
    }

    public function getAttributeList(): array
    {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter('is_used_for_promo_rules', 1);
        return $collection->getColumnValues('attribute_code');
    }

    public function getBatchSize(): int
    {
        $batchSize = (int)$this->deploymentConfig->get(
            self::DEPLOYMENT_CONFIG_INDEXER_BATCHES . \MageSuite\ProductSymbols\Model\Indexer\Product\Processor::INDEXER_ID
        );

        return $batchSize > 0 ? $batchSize : $this->batchSize;
    }
}
