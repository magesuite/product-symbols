<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Groups;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageSuite\ProductSymbols\Model\Groups',
            'MageSuite\ProductSymbols\Model\ResourceModel\Groups'
        );
    }
}