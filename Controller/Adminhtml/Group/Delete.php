<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Delete extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_delete';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        protected \Magento\Eav\Model\Config $eavConfig,
        protected \Magento\Framework\Controller\ResultFactory $resultRedirect
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        try {
            $params = $this->_request->getParams();
            $group = $this->groupRepository->getById($params['id']);
            $this->groupRepository->delete($group);
            $this->messageManager->addSuccessMessage(__('Group has been deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_url->getUrl('symbol/grid/group');
        $resultRedirect->setPath($url);

        return $resultRedirect;
    }
}
