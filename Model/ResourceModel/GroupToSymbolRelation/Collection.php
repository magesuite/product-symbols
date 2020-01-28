<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MageSuite\ProductSymbols\Model\GroupToSymbolRelation::class,
            \MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation::class
        );
    }
}
