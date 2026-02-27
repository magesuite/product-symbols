<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Indexer\Product\Action;

class Rows implements \Magento\Framework\Indexer\DimensionalIndexerInterface
{
    public function __construct(
        protected \MageSuite\ProductSymbols\Model\Indexer\Product\DataProvider $dataProvider,
        protected \MageSuite\ProductSymbols\Model\Indexer\Product\TableMaintainer $tableMaintainer,
        protected \MageSuite\ProductSymbols\Provider\Data\SymbolToProduct $productDataProvider,
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
        protected \Magento\Framework\Indexer\DimensionProviderInterface $dimensionProvider,
        protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel
    ) {}

    public function execute(array $entityIds): void
    {
        foreach ($this->dimensionProvider->getIterator() as $dimension) {
            $this->executeByDimensions($dimension, new \ArrayIterator($entityIds));
        }
    }

    public function executeByDimensions(array $dimensions, \Traversable $entityIds): void
    {
        $entityIds = iterator_to_array($entityIds);

        $storeId = (int) $dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $symbols = $this->productDataProvider->getSymbolsWithConditions($storeId);

        if (empty($symbols)) {
            $this->indexResourceModel->deleteByStoreId($storeId);
            return;
        }

        foreach (array_chunk($entityIds, $this->dataProvider->getBatchSize()) as $entityIdsChunk) {
            $this->prepareIndexTable($dimensions);
            foreach ($symbols as $symbol) {
                $this->buildIndexForSymbol($symbol, $dimensions, $entityIdsChunk);
            }

            $this->syncData($dimensions, $entityIds);
        }
    }

    protected function buildIndexForSymbol(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol, array $dimensions, array $productIds): void
    {
        $indexData = [];
        $storeId = (int) $dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $symbolId = $symbol->getId();
        $collection = $this->dataProvider->getProducts($dimensions, $symbol, $productIds);
        $ids = $collection->getAllIds();

        foreach ($ids as $productId) {
            $indexData[] = [
                'product_id' => $productId,
                'symbol_id' => $symbolId,
                'store_id' => $storeId
            ];
        }

        $indexBatches = array_chunk($indexData, $this->dataProvider->getBatchSize());

        foreach ($indexBatches as $batch) {
            $this->resourceConnection->getConnection()->insertMultiple(
                $this->tableMaintainer->getMainTmpTable($dimensions),
                $batch
            );
        }
    }

    protected function prepareIndexTable(array $dimensions): void
    {
        $this->tableMaintainer->createMainTmpTable($dimensions);
        $this->tableMaintainer->cleanTable(
            $this->tableMaintainer->getMainTmpTable($dimensions)
        );
    }

    protected function syncData(array $dimensions, array $entityIds): void
    {
        $storeId = (int)$dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $connection = $this->resourceConnection->getConnection();
        $connection->delete(
            $this->tableMaintainer->getMainTable(),
            [
                'product_id IN (?)' => $entityIds,
                'store_id = ?' => $storeId
            ]
        );
        $select = $connection->select()
            ->from(
                $this->tableMaintainer->getMainTmpTable($dimensions),
                ['*']
            );
        $connection->query(
            $connection->insertFromSelect(
                $select,
                $this->tableMaintainer->getMainTable(),
                []
            )
        );
        $this->tableMaintainer->dropTableForDimensions($dimensions);
    }
}
