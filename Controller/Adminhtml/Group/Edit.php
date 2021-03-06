<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_edit';

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
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;

        parent::__construct($context);
        $this->groupRepository = $groupRepository;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        
        $group = $this->getCurrentGroup($params);
        $this->registry->register('group', $group);

        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::group_menu');
        $isNew = (isset($params['id'])) ? false : true;

        if ($isNew) {
            $resultPage->getConfig()->getTitle()->prepend((__('New Symbols Group')));
            $resultPage->addBreadcrumb(__('New Symbols Group'), __('New Symbols Group'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Symbols Group')));
            $resultPage->addBreadcrumb(__('Edit Symbols Group'), __('Edit Symbols Group'));
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

    protected function getCurrentGroup($params)
    {
        $id = $params['id'] ?? 0;

        if (!is_numeric($id) || $id <= 0) {
            return null;
        }

        return $this->groupRepository->getById($id);
    }
}
