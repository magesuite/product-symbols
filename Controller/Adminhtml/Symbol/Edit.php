<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_edit';

    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    protected \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository;

    protected \Magento\Framework\Registry $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->symbolRepository = $symbolRepository;
        $this->registry = $registry;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store');

        $title = __('New Symbol');

        if ($id) {
            $symbol = $this->symbolRepository->getById($id, $storeId);

            if (!$symbol->getId()) {
                $this->messageManager->addErrorMessage(__('This symbol no longer exists.'));

                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

            $symbol->getConditions()->setFormName('symbol_edit_form');
            $symbol->getConditions()->setJsFormObject(
                $symbol->getConditionsFieldSetId($symbol->getConditions()->getFormName())
            );
            $this->registry->register('symbol', $symbol);
            $title = __('Edit Symbol %1', $symbol->getSymbolName());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb($title, $title);

        $resultPage->getConfig()->getTitle()->prepend(__('Symbols'));
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
