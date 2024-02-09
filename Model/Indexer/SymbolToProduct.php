<?php

namespace MageSuite\ProductSymbols\Model\Indexer;

class SymbolToProduct implements
    \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{
    public const INDEXER_ID = 'symbol_to_product';

    protected \MageSuite\ProductSymbols\Indexer\IndexBuilder $indexBuilder;
    protected \Magento\Catalog\Model\ResourceModel\Product $productResource;
    protected \MageSuite\ProductSymbols\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \MageSuite\ProductSymbols\Indexer\IndexBuilder $indexBuilder,
        \MageSuite\ProductSymbols\Helper\Configuration $configuration
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->productResource = $productResource;
        $this->configuration = $configuration;
    }

    public function execute($ids): void
    {
        $this->executeList($ids);
    }

    public function executeFull(): void
    {
        if (!$this->configuration->isIndexingEnabled()) {
            return;
        }

        $ids = array_column(
            $this->productResource->getProductEntitiesInfo(['entity_id']),
            'entity_id'
        );

        $this->indexBuilder->reindexList($ids);
    }

    public function executeList(array $ids): void
    {
        if (!$this->configuration->isIndexingEnabled()) {
            return;
        }

        $this->indexBuilder->reindexList($ids);
    }

    public function executeRow($id)
    {
        $this->executeList([$id]);
    }
}
