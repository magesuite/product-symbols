<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Edit;

class SaveButton extends \Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save Group'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
