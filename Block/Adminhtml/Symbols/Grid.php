<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbols;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_symbols';
        $this->_blockGroup = 'MageSuite_ProductSymbols';
        $this->_headerText = __('Posts');
        $this->_addButtonLabel = __('Create New Symbol');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/symbols/newsymbol');
    }
}