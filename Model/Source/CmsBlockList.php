<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Model\Source;

class CmsBlockList extends \Magento\Cms\Model\Config\Source\Block
{
    public function toOptionArray(): array
    {
        $emptyValue = [[
            'label' => __('-- Please Select --'),
            'value' => null,
        ]];
        $options = parent::toOptionArray();

        return array_merge($emptyValue, $options);
    }
}
