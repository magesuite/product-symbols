<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbols;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var bool
     */
    protected $resultPage = false;

    /**
     * @var \MageSuite\ProductSymbols\Model\Symbols\Processor\SaveFactory
     */
    protected $saveFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Model\Symbols\Processor\SaveFactory $saveFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->saveFactory = $saveFactory;

        parent::__construct($context);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        try {
            $params['is_api'] = false;

            $paramsObject = $this->dataObjectFactory->create();
            $paramsObject->setData($params);
            $this->saveFactory->create()->processSave($paramsObject);
            $this->messageManager->addSuccessMessage('Symbol has been saved');
        } catch (\Exception $e)
        {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId = isset($params['store_id']) ? $params['store_id'] : 0;
        $url = $this->_url->getUrl('customization/symbols/edit', ['id' => $params['entity_id'], 'store' => $storeId]);
        $resultRedirect->setPath($url);

        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}