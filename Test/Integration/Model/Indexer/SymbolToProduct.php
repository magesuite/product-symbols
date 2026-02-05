<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Test\Integration\Model\Indexer;

class SymbolToProduct extends \PHPUnit\Framework\TestCase
{
    protected const SIMPLE_PRODUCT_ID = 1;

    protected ?\Magento\Framework\App\ObjectManager $objectManager;
    protected ?\MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel;
    protected ?\Magento\Catalog\Model\ProductRepository $productRepository;
    protected ?\MageSuite\ProductSymbols\Model\SymbolRepository $symbolRepository;
    protected ?\MageSuite\ProductSymbols\Model\Indexer\SymbolToProduct $indexer;
    protected ?\Magento\Store\Model\StoreManagerInterface $storeManager;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->indexResourceModel = $this->objectManager->get(\MageSuite\ProductSymbols\Model\ResourceModel\Index::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
        $this->symbolRepository = $this->objectManager->get(\MageSuite\ProductSymbols\Model\SymbolRepository::class);
        $this->indexer = $this->objectManager->get(\MageSuite\ProductSymbols\Model\Indexer\SymbolToProduct::class);
        $this->storeManager = $this->objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testItDoesNotPutProductIntoIndexWhenConditionsDoNotApply(): void
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $this->indexer->execute([self::SIMPLE_PRODUCT_ID]);

        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID], $storeId);

        $this->assertTrue(!isset($symbols[self::SIMPLE_PRODUCT_ID]));
    }

    /**
     * @dataProvider getSymbolsDataProvider
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     */
    public function testItDoesPutProductIntoIndexWhenConditionsMatch(?string $store, array $expectedSymbolIds): void
    {
        $storeId = (int) $this->storeManager->getStore($store)->getId();
        $this->indexer->execute([self::SIMPLE_PRODUCT_ID]);

        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID], $storeId);

        $this->assertEquals($expectedSymbolIds, $symbols[self::SIMPLE_PRODUCT_ID] ?? []);
    }

    public static function getSymbolsDataProvider(): array
    {
        return [
            [null, [1101]],
            ['test333', []]
        ];
    }
}
