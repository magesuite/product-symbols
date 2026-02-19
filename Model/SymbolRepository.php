<?php

namespace MageSuite\ProductSymbols\Model;

class SymbolRepository implements \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface, \Magento\Framework\ObjectManager\ResetAfterRequestInterface
{
    protected array $instances = [];

    protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol $symbolResource;
    protected \MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory;
    protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $collectionFactory;
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \MageSuite\ProductSymbols\Model\Symbol\Processor\SaveFactory $saveFactory;
    protected \MageSuite\ProductSymbols\Model\Symbol\Processor\UploadFactory $uploadFactory;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol $symbolResource,
        \MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory,
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
        $cacheKey = $this->getCacheKey((int)$id, (int)$storeId);

        if (isset($this->instances[$cacheKey])) {
            return $this->instances[$cacheKey];
        }

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

        $this->instances[$cacheKey] = $symbol;

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

        $this->removeFromCache($symbol);

        return $symbol;
    }

    public function delete(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol): bool
    {
        try {
            $this->symbolResource->delete($symbol);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'Could not delete symbol: %1',
                    $e->getMessage()
                ),
                $e
            );
        }

        $this->removeFromCache($symbol);

        return true;
    }

    public function getAllSymbols($storeId = null): array
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('*')
            ->addAttributeToSort('sort_order');
        $entities = [];

        foreach ($collection as $item) {
            $entities[$item->getId()] = $item;
        }

        return $entities;
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

    public function update(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $symbolEntity = $this->getById($symbol->getEntityId(), $storeId);
        $symbolEntity->addData($symbol->getData());

        return $this->create($symbolEntity);
    }

    public function deleteById($id): bool
    {
        return $this->delete($this->getById($id));
    }

    public function _resetState(): void
    {
        $this->cleanCache();
    }

    public function cleanCache(): self
    {
        $this->instances = [];

        return $this;
    }

    protected function getCacheKey(int $symbolId, int $storeId): string
    {
        return sprintf('%s_%s', $symbolId, $storeId);
    }

    protected function removeFromCache(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol): self
    {
        $cacheKey = $this->getCacheKey(
            (int)$symbol->getEntityId(),
            (int)$symbol->getStoreId()
        );

        if (isset($this->instances[$cacheKey])) {
            unset($this->instances[$cacheKey]);
        }

        return $this;
    }
}
