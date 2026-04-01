<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Grid;

class Symbol extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol';

    protected ?\Magento\Framework\View\Result\Page $resultPage = null;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute() //phpcs:ignore
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::symbol_menu');
        $resultPage->getConfig()->getTitle()->prepend((__('Symbol')));

        $resultPage->addBreadcrumb(__('Symbol'), __('Symbol'));

        return $resultPage;
    }

    public function getResultPage(): \Magento\Framework\View\Result\Page
    {
        if (!$this->resultPage) {
            $this->resultPage = $this->pageFactory->create();
        }

        return $this->resultPage;
    }
}
