<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Grid;

class Group extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group';

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
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::group_menu');
        $resultPage->getConfig()->getTitle()->prepend((__('Symbol groups')));

        $resultPage->addBreadcrumb(__('Symbol group'), __('Symbol group'));

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
