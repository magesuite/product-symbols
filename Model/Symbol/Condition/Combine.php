<?php

namespace MageSuite\ProductSymbols\Model\Symbol\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\Product
     */
    protected $conditionProduct;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CatalogRule\Model\Rule\Condition\Product $conditionProduct,
        array $data = []
    ) {
        $this->setType(\Magento\SalesRule\Model\Rule\Condition\Combine::class);

        $this->conditionProduct = $conditionProduct;

        parent::__construct($context, $data);
    }

    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->conditionProduct->loadAttributeOptions()->getAttributeOption();

        $valueAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $valueAttributes[] = [
                'value' => 'Magento\CatalogRule\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();

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
