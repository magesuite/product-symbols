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

        foreach (array_chunk($entityIds, $this->dataProvider->getBatchSize()) as $entityIdsChunk) {
            $collection = $this->dataProvider->getProducts($dimensions, $entityIdsChunk, 0);
            $this->prepareIndexTable($dimensions);
            $this->buildIndex($dimensions, $collection);
            $this->syncData($dimensions, $entityIds);
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

    protected function buildIndex(array $dimensions, \Magento\Catalog\Model\ResourceModel\Product\Collection $products): void
    {
        $storeId = (int) $dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $symbols = $this->productDataProvider->getSymbolsWithConditions($storeId);

        if (empty($symbols)) {
            $this->indexResourceModel->deleteByStoreId($storeId);
            return;
        }

        $toInsertSymbols = [];

        foreach ($products as $product) {
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

        $products->clear();

        if (empty($toInsertSymbols)) {
            return;
        }

        $this->resourceConnection->getConnection()->insertMultiple(
            $this->tableMaintainer->getMainTmpTable($dimensions),
            $toInsertSymbols
        );
    }
}
