<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Group;

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
            'MageSuite\ProductSymbols\Model\Group',
            'MageSuite\ProductSymbols\Model\ResourceModel\Group'
        );
    }
}