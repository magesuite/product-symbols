<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Symbols;

class Collection extends \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageSuite\ProductSymbols\Model\Symbols',
            'MageSuite\ProductSymbols\Model\ResourceModel\Symbols'
        );
    }
}