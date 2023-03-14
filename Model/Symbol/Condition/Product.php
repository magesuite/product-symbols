<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Symbol\Condition;

class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    protected function _setAttributeValue(\Magento\Framework\Model\AbstractModel $model)
    {
        $attributeCode = $this->getAttributeObject()->getAttributeCode();
        $value = $model->getData($attributeCode);
        $value = $this->_prepareDatetimeValue($value, $model);
        $value = $this->_prepareMultiselectValue($value, $model);
        $model->setData($this->getAttribute(), $value);

        return $this;
    }
}
