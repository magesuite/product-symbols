<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Indexer\Product\Action;

class Full implements \Magento\Framework\Indexer\DimensionalIndexerInterface
{
    public function __construct(
        protected \MageSuite\ProductSymbols\Model\Indexer\Product\DataProvider $dataProvider,
        protected \MageSuite\ProductSymbols\Model\Indexer\Product\TableMaintainer $tableMaintainer,
        protected \MageSuite\ProductSymbols\Provider\Data\SymbolToProduct $productDataProvider,
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
        protected \Magento\Framework\Indexer\DimensionProviderInterface $dimensionProvider,
        protected \Magento\Indexer\Model\ProcessManager $processManager,
        protected \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher $activeTableSwitcher,
        protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel
    ) {}

    public function execute(): \MageSuite\ProductSymbols\Model\Indexer\Product\Action\Full
    {
        $this->clearReplicaTable();
        $this->reindex();
        $this->switchTables();

        return $this;
    }

    protected function clearReplicaTable(): void
    {
        $replicaTable = $this->tableMaintainer->getMainReplicaTable();
        $this->tableMaintainer->cleanTable($replicaTable);
    }

    public function reindex(): void
    {
        $userFunctions = [];

        foreach ($this->dimensionProvider->getIterator() as $dimension) {
            $userFunctions[] = function () use ($dimension): void {
                $this->executeByDimensions($dimension);
            };
        }

        $this->processManager->execute($userFunctions);
    }

    public function executeByDimensions(array $dimensions, ?\Traversable $entityIds = null): void
    {
        $storeId = (int) $dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $symbols = $this->productDataProvider->getSymbolsWithConditions($storeId);

        if (empty($symbols)) {
            $this->indexResourceModel->deleteByStoreId($storeId);
            return;
        }

        foreach ($symbols as $symbol) {
            $this->buildIndexForSymbol($symbol, $dimensions);
        }
    }

    protected function buildIndexForSymbol(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol, array $dimensions): void
    {
        $indexData = [];
        $storeId = (int) $dimensions[\Magento\Store\Model\StoreDimensionProvider::DIMENSION_NAME]->getValue();
        $symbolId = $symbol->getId();
        $collection = $this->dataProvider->getProducts($dimensions, $symbol);
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
                $this->tableMaintainer->getMainReplicaTable(),
                $batch
            );
        }
    }

    protected function switchTables(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $this->activeTableSwitcher->switchTable(
            $connection,
            [$this->tableMaintainer->getMainTable()]
        );
    }
}
