<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Group;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'MageSuite_ProductSymbols';
        $this->_headerText = __('Groups');
        $this->_addButtonLabel = __('Create New Group');
        parent::_construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/group/newgroup');
    }
}
