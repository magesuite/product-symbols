<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbol;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_symbol';
        $this->_blockGroup = 'MageSuite_ProductSymbols';
        $this->_headerText = __('Symbols');
        $this->_addButtonLabel = __('Create New Symbol');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/symbol/newsymbol');
    }
}