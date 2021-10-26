<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_edit';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->groupRepository = $groupRepository;
        $this->registry = $registry;

        parent::__construct($context);
    }

    public function execute()
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
