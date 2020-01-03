<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel;

class Groups extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
//    public function __construct(
//        \Magento\Framework\Model\ResourceModel\Db\Context $context
//    )
//    {
//        parent::__construct($context);
//    }
//
    protected function _construct()
    {
        $this->_init('symbols_group', 'entity_id');
    }
}