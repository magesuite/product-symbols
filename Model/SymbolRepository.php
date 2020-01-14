<?php

namespace MageSuite\ProductSymbols\Model;

class SymbolRepository implements \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
{
    const DEFAULT_STORE_ID = 0;

    protected $instances = [];

    protected $symbolAttributes = [
        'entity_id',
        'store_id',
        'symbol_name',
        'symbol_icon',
        'symbol_icon_url',
        'symbol_short_description',
        'symbol_groups',
    ];
    /**
     * @var ResourceModel\Symbol
     */
    private $symbolResource;
    /**
     * @var SymbolFactory
     */
    private $symbolFactory;
    /**
     * @var ResourceModel\Symbol\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Symbol\Processor\SaveFactory
     */
    private $saveFactory;

    /**
     * @var Symbol\Processor\UploadFactory
     */
    private $uploadFactory;


    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol $symbolResource,
        SymbolFactory $symbolFactory,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\SaveFactory $saveFactory,
        \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory $uploadFactory
    )
    {

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

        /**
         * Symbol is loaded twice, first for requested store id, second for default store view.
         * This is done to get proper data if params for store id is empty or not fulfilled.
         * If symbol is not additionally loaded for default store view data is not taken correctly.
         */
        $symbol->getResource()->setDefaultStoreId($storeId);
        $symbol->load($id);
        $symbol->getResource()->setDefaultStoreId(self::DEFAULT_STORE_ID);
        $symbol->load($id);

        if (!$symbol->getEntityId()) {
            return null;
        }

        return $symbol;
    }


    public function save(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        try {
            $isExists = ($this->getById($symbol['entity_id'])) ? true : false;
            if (!$isExists) {
                $this->symbolResource->save($symbol);
            }
            
            $attributesToRemove = $this->symbolAttributes;
            foreach ($symbol->getData() as $key => $value) {
                $attr = $this->symbolResource->getAttribute($key);
                $attributeIndex = array_search($key, $attributesToRemove);
                if (false !== $attributeIndex) {
                    unset($attributesToRemove[$attributeIndex]);
                }

                if (!$attr) {
                    continue;
                }

                $this->symbolResource->updateAttribute($symbol, $attr, $value, $symbol->getStoreId());
            }
            $this->symbolResource->removeAttribute($symbol, $attributesToRemove);
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
        if($storeId == null) {
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

            if($symbol->getSymbolIconEncodedData()) {
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