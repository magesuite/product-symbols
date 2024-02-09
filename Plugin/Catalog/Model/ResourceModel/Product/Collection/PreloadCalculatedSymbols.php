<?php

namespace MageSuite\ProductSymbols\Plugin\Catalog\Model\ResourceModel\Product\Collection;

class PreloadCalculatedSymbols
{
    protected \MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResource;

    public function __construct(\MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResource)
    {
        $this->indexResource = $indexResource;
    }

    public function afterGetItems(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject, $result)
    {
        if ($subject->hasFlag('symbols_from_index_preloaded')) {
            return $result;
        }

        $subject->setFlag('symbols_from_index_preloaded', true);
        $productIds = [];

        foreach ($result as $item) {
            $productIds[] = $item->getId();
        }

        if (empty($productIds)) {
            return $result;
        }

        $symbolsFromIndex = $this->indexResource->getByProductIds($productIds);

        foreach ($result as $item) {
            $productId = $item->getEntityId();

            $item->setSymbolsFromIndex($symbolsFromIndex[$productId] ?? []);
        }

        return $result;
    }
}
