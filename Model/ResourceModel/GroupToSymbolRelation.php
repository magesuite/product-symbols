<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel;

class GroupToSymbolRelation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_group_to_symbol_relation', 'entity_id');
    }
}
