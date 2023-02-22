<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_save';

    protected \MageSuite\ProductSymbols\Model\Symbol\Processor\SaveFactory $saveFactory;

    protected \Magento\Framework\DataObjectFactory $dataObjectFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\SaveFactory $saveFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);

        $this->saveFactory = $saveFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function execute()
    {
        $params = $this->_request->getParams();
        $storeId = $params['store_id'] ?? 0;
        $routeParams = [
            'store' => $storeId
        ];

        try {
            $params['is_api'] = false;

            $paramsObject = $this->dataObjectFactory->create();
            $paramsObject->setData($params);
            $symbol = $this->saveFactory->create()->processSave($paramsObject);
            $routeParams['id'] = $symbol->getEntityId();

            $this->messageManager->addSuccessMessage('Symbol has been saved');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_url->getUrl('symbol/symbol/edit', $routeParams);
        $resultRedirect->setPath($url);

        return $resultRedirect;
    }
}
