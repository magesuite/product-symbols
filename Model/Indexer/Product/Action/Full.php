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
            $userFunctions[] = function () use ($dimension) {
                $this->executeByDimensions($dimension);
            };
        }

        $this->processManager->execute($userFunctions);
    }

    public function executeByDimensions(array $dimensions, ?\Traversable $entityIds = null): void
    {
        $lastProductId = 0;

        while (true) {
            $collection = $this->dataProvider->getProducts($dimensions, null, $lastProductId);

            if ($collection->count() === 0) {
                break;
            }

            $lastProductId = (int)$collection->getLastItem()->getId();
            $this->buildIndex($dimensions, $collection);
        }
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
            $this->tableMaintainer->getMainReplicaTable(),
            $toInsertSymbols
        );
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
