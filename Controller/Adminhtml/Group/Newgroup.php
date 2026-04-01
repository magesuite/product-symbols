<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Group;

class Newgroup extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::group_edit';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        protected \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
