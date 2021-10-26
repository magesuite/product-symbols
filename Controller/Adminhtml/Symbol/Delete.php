<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_delete';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Controller\ResultFactory $resultRedirect
    ) {
        $this->pageFactory = $pageFactory;
        $this->eavConfig = $eavConfig;
        $this->resultRedirect = $resultRedirect;
        $this->symbolRepository = $symbolRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $params = $this->_request->getParams();

            $symbol = $this->symbolRepository->getById($params['id']);

            $this->symbolRepository->delete($symbol);

            $this->messageManager->addSuccessMessage('Symbol has been deleted');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        $url = $this->_url->getUrl('symbol/grid/symbol');

        $resultRedirect->setPath($url);

        return $resultRedirect;
    }
}
