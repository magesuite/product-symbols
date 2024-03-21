<?php

namespace MageSuite\ProductSymbols\Model;

class PreloadCalculatedSymbols
{
    protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResource;

    public function __construct(\MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResource)
    {
        $this->indexResource = $indexResource;
    }

    public function execute(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        if ($collection->hasFlag('symbols_from_index_preloaded')) {
            return $collection;
        }

        $collection->setFlag('symbols_from_index_preloaded', true);
        $productIds = [];

        foreach ($collection->getItems() as $item) {
            $productIds[] = $item->getId();
        }

        if (empty($productIds)) {
            return $collection;
        }

        $symbolsFromIndex = $this->indexResource->getByProductIds($productIds);

        foreach ($collection as $item) {
            $productId = $item->getEntityId();

            $item->setSymbolsFromIndex($symbolsFromIndex[$productId] ?? []);
        }

        return $collection;
    }
}
