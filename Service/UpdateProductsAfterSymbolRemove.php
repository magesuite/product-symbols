<?php
namespace MageSuite\ProductSymbols\Service;

class UpdateProductsAfterSymbolRemove
{
    private $storeId;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $eavAttribute;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;
    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    private $productAction;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->eavAttribute = $eavAttribute;
        $this->eavConfig = $eavConfig;
        $this->productResource = $productResource;
        $this->productAction = $productAction;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    public function updateProducts($brandId)
    {
        $stores = $this->storeManager->getStores(true);

        foreach ($stores as $store) {
            $productIdsToUpdate = $this->prepareIdsToUpdate($brandId, $store->getId());

            if (empty($productIdsToUpdate)) {
                continue;
            }

            foreach ($productIdsToUpdate as $productId) {
                $this->updateProductBrandAttribute($productId, $brandId, $store->getId());
            }
        }
    }

    public function updateProductBrandAttribute($productId, $brandId, $storeId)
    {
        $brands = $this->prepareBrands($productId, $brandId, $storeId);

        $product = $this->productRepository->getById($productId);

        $product->setBrand($brands);

        $this->productRepository->save($product);
//        $this->productAction->updateAttributes(
//            [$productId],
//            ['brand' => $brands],
//            $storeId
//        );
    }

    public function prepareIdsToUpdate($brandId, $storeId)
    {
        $connection = $this->resourceConnection->getConnection();

        $entityType = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY);
        $brandAttribute = $this->eavAttribute->loadByCode($entityType, \MageSuite\BrandManagement\Model\Brand::BRAND_ATTRIBUTE_CODE);

        $select = $connection->select()
            ->from(['main_table' => $connection->getTableName('catalog_product_entity_varchar')])
            ->where('main_table.attribute_id = ?', $brandAttribute->getAttributeId())
            ->where('main_table.store_id = ?', $storeId)
            ->where('main_table.value LIKE "%?%"', $brandId);

        $result = $connection->fetchAll($select);

        return array_column($result, 'row_id');
    }

    public function prepareBrands($productId, $brandId, $storeId)
    {
        $product = $this->productRepository->getById($productId);
        $brands = $product->getBrand();
        $brands2 = $this->productResource->getAttributeRawValue((int) $productId, 'brand', (int) $storeId);
        var_dump($storeId);
        var_dump($productId);
        var_dump($brandId);
        var_dump($brands);
        var_dump($brands2);
        if(!is_array($brands)) {
            $brands = explode(',', $brands);
        }
        var_dump($brands);

        $brands = array_diff($brands, [$brandId]);

        var_dump($brands);
        return implode(',', $brands);
    }
}