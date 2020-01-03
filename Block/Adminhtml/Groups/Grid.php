<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Groups;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_groups';
        $this->_blockGroup = 'MageSuite_ProductSymbols';
        $this->_headerText = __('Groups');
        $this->_addButtonLabel = __('Create New Group');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/groups/newgroup');
    }
}