<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Symbol\Attribute;

class Collection extends \Magento\Eav\Model\ResourceModel\Attribute\Collection
{
    protected $_entityTypeCode = 'symbol'; //phpcs:ignore

    protected function _getEntityTypeCode()
    {
        return $this->_entityTypeCode;
    }

    protected function _getEavWebsiteTable()
    {
        return $this->getTable('symbol_eav_attribute_website');
    }
}
