<?php

namespace MageSuite\ProductSymbols\Model;

class SymbolRepository implements \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
{
    protected $instances = [];

    /**
     * @var ResourceModel\Symbol
     */
    protected $symbolResource;

    /**
     * @var SymbolFactory
     */
    protected $symbolFactory;

    /**
     * @var ResourceModel\Symbol\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Symbol\Processor\SaveFactory
     */
    protected $saveFactory;

    /**
     * @var Symbol\Processor\UploadFactory
     */
    protected $uploadFactory;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol $symbolResource,
        SymbolFactory $symbolFactory,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\SaveFactory $saveFactory,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory $uploadFactory
    ) {
        $this->symbolResource = $symbolResource;
        $this->symbolFactory = $symbolFactory;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->saveFactory = $saveFactory;
        $this->uploadFactory = $uploadFactory;
    }

    public function getById($id, $storeId = null)
    {
        $symbol = $this->symbolFactory->create();

        if (null !== $storeId) {
            $symbol->setData('store_id', $storeId);
        }
        $symbol->getResource()->setDefaultStoreId($storeId);
        $symbol->load($id);
        $symbol->getResource()->setDefaultStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $symbol->load($id);

        if (!$symbol->getEntityId()) {
            return null;
        }

        return $symbol;
    }

    public function save(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        try {
            $isNew = $this->getById($symbol->getEntityId()) ? false : true;

            $symbol->setIsNew($isNew);

            $this->symbolResource->save($symbol);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save symbol: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        return $symbol;
    }

    public function delete(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        $symbolFactory = $this->symbolFactory->create();
        $symbolFactory->setData($symbol->getData());

        try {
            $this->symbolResource->delete($symbolFactory);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'Could not delete symbol: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
    }

    public function getAllSymbols($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        
        $symbolCollection = $this->collectionFactory->create();
        $symbolCollection->setStoreId($storeId);
        $symbolCollection->addAttributeToSelect('*');

        $symbolDataArray = [];
        foreach ($symbolCollection as $symbol) {
            $symbolDataArray[$symbol->getEntityId()] = $symbol;
        }

        return $symbolDataArray;
    }

    public function create(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        try {
            $symbol['is_api'] = true;
            $uploader = $this->uploadFactory->create();

            if ($symbol->getSymbolIconEncodedData()) {
                $symbol->setSymbolIcon($uploader->processUpload($symbol->getSymbolIconEncodedData()));
            }

            $symbol = $this->saveFactory->create()->processSave($symbol);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save symbol.', $e->getMessage()), $e);
        }
        return $symbol;
    }

    public function update(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $symbolEntity = $this->getById($symbol->getEntityId(), $storeId);

        $symbolEntity->addData($symbol->getData());

        return $this->create($symbolEntity);
    }

    public function deleteById($id)
    {
        $symbol = $this->getById($id);
        $this->delete($symbol);
    }
}
