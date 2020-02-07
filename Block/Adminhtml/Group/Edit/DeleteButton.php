<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Edit;

class DeleteButton extends \Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{

    public function getButtonData()
    {
        $data = [];
        if ($this->getGroupId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf("deleteConfirm('%s', '%s')", __('Are you sure you want to do this?'), $this->getDeleteUrl()),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/group/delete', ['id' => $this->getGroupId()]);
    }

    public function getGroupId()
    {
        $params = $this->context->getRequest()->getParams();
        if (!isset($params['id'])) {
            return null;
        }

        return $params['id'];
    }
}
