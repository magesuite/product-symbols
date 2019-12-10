<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Grid;

class Index extends \Magento\Framework\App\Action\Action
{


    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    protected $resultPage = false;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::symbols_menu');
        $resultPage->getConfig()->getTitle()->prepend((__('Symbols')));

        $resultPage->addBreadcrumb(__('Symbols'), __('Symbols'));
        $resultPage->addBreadcrumb(__('Symbols'), __('Symbols'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function getResultPage()
    {
        if (!$this->resultPage) {
            $this->resultPage = $this->pageFactory->create();
        }
        return $this->resultPage;
    }

}