<?php

namespace MageSuite\ProductSymbols\Test\Integration\Indexer;

class IndexBuilderTest extends \PHPUnit\Framework\TestCase
{
    const SIMPLE_PRODUCT_ID = 1;

    protected ?\Magento\Framework\App\ObjectManager $objectManager;
    protected ?\MageSuite\ProductSymbols\Model\ResourceModel\Index $indexResourceModel;
    protected ?\Magento\Catalog\Model\ProductRepository $productRepository;
    protected ?\MageSuite\ProductSymbols\Model\SymbolRepository $symbolRepository;
    protected ?\MageSuite\ProductSymbols\Indexer\IndexBuilder $indexBuilder;
    protected ?\Magento\Store\Model\StoreManagerInterface $storeManager;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->indexResourceModel = $this->objectManager->get(\MageSuite\ProductSymbols\Model\ResourceModel\Index::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
        $this->symbolRepository = $this->objectManager->create(\MageSuite\ProductSymbols\Model\SymbolRepository::class);
        $this->indexBuilder = $this->objectManager->create(\MageSuite\ProductSymbols\Indexer\IndexBuilder::class);
        $this->storeManager = $this->objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testItDoesNotPutProductIntoIndexWhenConditionsDoNotApply()
    {
        $this->indexBuilder->reindexList([self::SIMPLE_PRODUCT_ID]);
        $storeId = $this->storeManager->getStore()->getId();
        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID], $storeId);

        $this->assertTrue(!isset($symbols[self::SIMPLE_PRODUCT_ID]));
    }

    /**
     * @dataProvider getSymbolsDataProvider
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     */
    public function testItDoesPutProductIntoIndexWhenConditionsMatch($store, $expectedSymbolIds)
    {
        $storeId = $this->storeManager->getStore($store)->getId();
        $this->indexBuilder->reindexList([self::SIMPLE_PRODUCT_ID]);
        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID], $storeId);

        $this->assertEquals($expectedSymbolIds, $symbols[self::SIMPLE_PRODUCT_ID] ?? []);
    }

    protected function getSymbolsDataProvider(): array
    {
        return [
            [null, [1101]],
            ['test333', []]
        ];
    }
}
