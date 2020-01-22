<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Symbol;

class Collection extends \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MageSuite\ProductSymbols\Model\Symbol::class,
            \MageSuite\ProductSymbols\Model\ResourceModel\Symbol::class
        );
    }
}
