<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Upload extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'MageSuite_ProductSymbols::symbol_edit';

    /**
     * @var \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory
     */
    protected $uploadProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory $uploadProcessor
    ) {
        $this->uploadProcessor = $uploadProcessor;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $result = $this->uploadProcessor->create()->processUpload();
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData($result);
    }
}
