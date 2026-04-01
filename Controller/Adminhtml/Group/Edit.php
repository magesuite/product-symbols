<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Edit extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_edit';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        protected \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        protected \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('id');
        $title = __('New Symbol Group');

        if ($id) {
            $group = $this->groupRepository->getById($id);

            if (!$group->getId()) {
                $this->messageManager->addErrorMessage(__('This group no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

            $this->registry->register('group', $group);
            $title = __('Edit Symbol Group %1', $group->getGroupName());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb($title, $title);
        $resultPage->getConfig()->getTitle()->prepend(__('Symbol Groups'));
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
