<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Group;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            \MageSuite\ProductSymbols\Model\Group::class,
            \MageSuite\ProductSymbols\Model\ResourceModel\Group::class
        );
    }
}
