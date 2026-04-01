<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Save extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_save';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \MageSuite\ProductSymbols\Model\Group\Processor\SaveFactory $saveFactory,
        protected \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $params = $this->getRequest()->getParams();
        $routeParams = null;

        try {
            $group = $this->saveFactory->create()->processSave($params);
            $this->messageManager->addSuccessMessage('Symbols group has been saved');
            $routeParams = [
                'id' => $group->getEntityId()
            ];
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_url->getUrl('symbol/group/edit', $routeParams);
        $resultRedirect->setPath($url);

        return $resultRedirect;
    }
}
