<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_edit';

    protected $resultPage = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;
        $this->symbolRepository = $symbolRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        
        $symbol = $this->getCurrentSymbol($params);
        $this->registry->register('symbol', $symbol);

        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::symbol_menu');
        $isNew = (isset($params['id'])) ? false : true;

        if ($isNew) {
            $resultPage->getConfig()->getTitle()->prepend((__('New Symbol')));
            $resultPage->addBreadcrumb(__('New Symbol'), __('New Symbol'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Symbol')));
            $resultPage->addBreadcrumb(__('Edit Symbol'), __('Edit Symbol'));
        }

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    public function getResultPage()
    {
        if (!$this->resultPage) {
            $this->resultPage = $this->pageFactory->create();
        }
        return $this->resultPage;
    }

    protected function getCurrentSymbol($params)
    {
        $id = $params['id'] ?? 0;

        if (!is_numeric($id) || $id <= 0) {
            return null;
        }

        if (isset($params['store'])) {
            $storeId = $params['store'];
        } else {
            $storeId = 0;
        }

        return $this->symbolRepository->getById($id, $storeId);
    }
}
