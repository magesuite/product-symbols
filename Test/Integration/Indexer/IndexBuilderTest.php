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

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->indexResourceModel = $this->objectManager->get(\MageSuite\ProductSymbols\Model\ResourceModel\Index::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
        $this->symbolRepository = $this->objectManager->create(\MageSuite\ProductSymbols\Model\SymbolRepository::class);
        $this->indexBuilder = $this->objectManager->create(\MageSuite\ProductSymbols\Indexer\IndexBuilder::class);
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

        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID]);

        $this->assertTrue(!isset($symbols[self::SIMPLE_PRODUCT_ID]));
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     */
    public function testItDoesPutProductIntoIndexWhenConditionsMatch()
    {
        $this->indexBuilder->reindexList([self::SIMPLE_PRODUCT_ID]);

        $symbols = $this->indexResourceModel->getByProductIds([self::SIMPLE_PRODUCT_ID]);

        $this->assertEquals([1101], $symbols[self::SIMPLE_PRODUCT_ID]);
    }
}
