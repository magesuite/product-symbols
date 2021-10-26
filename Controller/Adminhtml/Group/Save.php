<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_save';

    /**
     * @var \MageSuite\ProductSymbols\Model\Group\Processor\SaveFactory
     */
    protected $saveFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Model\Group\Processor\SaveFactory $saveFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->saveFactory = $saveFactory;

        parent::__construct($context);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function execute()
    {
        $params = $this->_request->getParams();
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
