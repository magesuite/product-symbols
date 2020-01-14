<?php

namespace MageSuite\ProductSymbols\Controller\Adminhtml\Symbol;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory
     */
    protected $uploadProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory $uploadProcessor
    )
    {
        $this->uploadProcessor = $uploadProcessor;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultFactory
     */
    public function execute()
    {
        try {
            $result = $this->uploadProcessor->create()->processUpload();
        } catch (\Exception $e)
        {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}