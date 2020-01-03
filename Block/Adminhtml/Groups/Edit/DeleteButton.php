<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Groups\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Cms\Block\Adminhtml\Block\Edit\GenericButton;

class DeleteButton extends GenericButton implements ButtonProviderInterface
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
        return $this->getUrl('*/groups/delete', ['id' => $this->getGroupId()]);
    }


    public function getGroupId()
    {
        $params = $this->context->getRequest()->getParams();
        if(!isset($params['id'])){
            return null;
        }

        return $params['id'];
    }
}
