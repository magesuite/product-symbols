<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel;

class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('symbols_group', 'entity_id');
    }
}