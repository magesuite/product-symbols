<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Symbol\Condition;

class Combine extends \Magento\CatalogRule\Model\Rule\Condition\Combine
{
    public function getNewChildSelectOptions(): array
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();

        $valueAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $valueAttributes[] = [
                'value' => 'MageSuite\ProductSymbols\Model\Symbol\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [parent::getNewChildSelectOptions()[0]];

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \MageSuite\ProductSymbols\Model\Symbol\Condition\Combine::class,
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $valueAttributes]
            ]
        );

        return $conditions;
    }
}
