<?php

namespace MageSuite\ProductSymbols\Test\Integration\Plugin\Catalog\Model\ResourceModel\Product\Collection;

class PreloadCalculatedSymbolsTest extends \PHPUnit\Framework\TestCase
{
    const SIMPLE_PRODUCT_ID = 1;

    protected ?\Magento\Framework\App\ObjectManager $objectManager;
    protected ?\Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection;
    protected ?\MageSuite\ProductSymbols\Model\SymbolRepository $symbolRepository;
    protected ?\MageSuite\ProductSymbols\Indexer\IndexBuilder $indexBuilder;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productCollection = $this->objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $this->symbolRepository = $this->objectManager->create(\MageSuite\ProductSymbols\Model\SymbolRepository::class);
        $this->indexBuilder = $this->objectManager->create(\MageSuite\ProductSymbols\Indexer\IndexBuilder::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     */
    public function testPreloadingDoesNotHappenByDefault()
    {
        $this->indexBuilder->reindexList([self::SIMPLE_PRODUCT_ID]);

        $products = $this->productCollection->addIdFilter([self::SIMPLE_PRODUCT_ID])->getItems();

        $simpleProduct = array_shift($products);
        $this->assertNull($simpleProduct->getSymbolsFromIndex());
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     */
    public function testValidationCalculationDoesNotHappenWhenProductWasIndexed()
    {
        $this->indexBuilder->reindexList([self::SIMPLE_PRODUCT_ID]);

        $products = $this->productCollection->addIdFilter([self::SIMPLE_PRODUCT_ID])->getItems();

        $simpleProduct = array_shift($products);
        $symbol = $this->symbolRepository->getById(1101);
        // preload should happen below
        $symbol->validate($simpleProduct);

        $this->assertEquals([1101], $simpleProduct->getSymbolsFromIndex());

        $simpleProduct->setMetaDescription('value_should_no_longer_match_symbol');

        // validation should take data from index
        $this->assertTrue($symbol->validate($simpleProduct));

        // validation will be forced and data from index will be skipped
        $symbol->setForceValidation(true);
        $this->assertFalse($symbol->validate($simpleProduct));
    }
}
