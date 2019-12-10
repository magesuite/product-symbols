<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbols;

class Edit extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $pageFactory;

    protected $resultPage = false;



    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \MageSuite\ProductSymbols\Model\Symbols
     */
    private $symbols;
    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface
     */
    private $symbolsRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Model\Symbols $symbols,
        \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface $symbolsRepository,
        \Magento\Framework\Registry $registry
    )
    {
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;
        $this->symbols = $symbols;
        $this->symbolsRepository = $symbolsRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        
        $symbol = $this->getCurrentSymbol($params);
        $this->registry->register('symbol', $symbol);

        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::symbols_menu');
        $isNew = (isset($params['id'])) ? false : true;


        if ($isNew) {
            $resultPage->getConfig()->getTitle()->prepend((__('New Symbol')));
        } else {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Symbol')));
        }

        $resultPage->addBreadcrumb(__('Symbol'), __('Symbol'));
        $resultPage->addBreadcrumb(__('Symbol'), __('Symbol'));

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

    protected function getCurrentSymbol($params) {
        $id = $params['id'] ?? 0;

        if(!is_numeric($id) or $id <= 0) {
            return null;
        }

        if(isset($params['store'])){
            $storeId = $params['store'];
        } else {
            $storeId = 0;
        }

        return $this->symbolsRepository->getById($id, $storeId);
    }

}