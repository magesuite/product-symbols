<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Groups;

class Delete extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $pageFactory;

    /**
     * @var bool
     */
    protected $resultPage = false;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Controller\ResultFactory $resultRedirect
    )
    {
        $this->pageFactory = $pageFactory;
        $this->eavConfig = $eavConfig;
        $this->resultRedirect = $resultRedirect;
        $this->groupsRepository = $groupsRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $params = $this->_request->getParams();

            $group = $this->groupsRepository->getById($params['id']);

            $this->groupsRepository->delete($group);

            $this->messageManager->addSuccessMessage('Group has been deleted');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        $url = $this->_url->getUrl('symbols/grid/groups');

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