<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_edit';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->symbolRepository = $symbolRepository;
        $this->registry = $registry;

        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $title = __('New Symbol');

        if ($id) {
            $symbol = $this->symbolRepository->getById($id);

            if (!$symbol->getId()) {
                $this->messageManager->addErrorMessage(__('This symbol no longer exists.'));

                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

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
