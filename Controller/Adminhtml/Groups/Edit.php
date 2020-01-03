<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Groups;

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
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;
    /**
     * @var \MageSuite\ProductSymbols\Model\Symbols
     */
    private $symbols;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Model\Symbols $symbols,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository,
        \Magento\Framework\Registry $registry
    )
    {
        $this->pageFactory = $pageFactory;
        $this->registry = $registry;
        $this->symbols = $symbols;

        parent::__construct($context);
        $this->groupsRepository = $groupsRepository;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        
        $symbol = $this->getCurrentGroup($params);
        $this->registry->register('group', $symbol);

        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('MageSuite_ProductSymbols::groups_menu');
        $isNew = (isset($params['id'])) ? false : true;


        if ($isNew) {
            $resultPage->getConfig()->getTitle()->prepend((__('New Symbols Group')));
        } else {
            $resultPage->getConfig()->getTitle()->prepend((__('Edit Symbols Group')));
        }

        $resultPage->addBreadcrumb(__('New Symbols Group'), __('New Symbols Group'));
        $resultPage->addBreadcrumb(__('New Symbols Group'), __('New Symbols Group'));

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

    protected function getCurrentGroup($params) {
        $id = $params['id'] ?? 0;

        if(!is_numeric($id) or $id <= 0) {
            return null;
        }

        return $this->groupsRepository->getById($id);
    }

}