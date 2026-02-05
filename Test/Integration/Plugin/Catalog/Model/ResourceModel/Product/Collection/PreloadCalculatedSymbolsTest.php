<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Test\Integration\Plugin\Catalog\Model\ResourceModel\Product\Collection;

class PreloadCalculatedSymbolsTest extends \PHPUnit\Framework\TestCase
{
    protected const SIMPLE_PRODUCT_ID = 1;

    protected ?\Magento\Framework\App\ObjectManager $objectManager;
    protected ?\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;
    protected ?\MageSuite\ProductSymbols\Model\SymbolRepository $symbolRepository;
    protected ?\MageSuite\ProductSymbols\Model\Indexer\SymbolToProduct $indexer;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productCollectionFactory = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class);
        $this->symbolRepository = $this->objectManager->get(\MageSuite\ProductSymbols\Model\SymbolRepository::class);
        $this->indexer = $this->objectManager->get(\MageSuite\ProductSymbols\Model\Indexer\SymbolToProduct::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation disabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     * @magentoConfigFixture default_store cataloginventory/options/show_out_of_stock 1
     */
    public function testPreloadingDoesNotHappenByDefault(): void
    {
        $this->indexer->execute([self::SIMPLE_PRODUCT_ID]);

        $products = $this->productCollectionFactory->create()->addIdFilter(self::SIMPLE_PRODUCT_ID)->getItems();
        $simpleProduct = array_shift($products);
        $this->assertNull($simpleProduct->getSymbolsFromIndex());
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation disabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/product_with_test_attribute.php
     * @magentoConfigFixture default_store cataloginventory/options/show_out_of_stock 1
     */
    public function testValidationCalculationDoesNotHappenWhenProductWasIndexed(): void
    {
        $this->indexer->execute([self::SIMPLE_PRODUCT_ID]);

        $products = $this->productCollectionFactory->create()->addIdFilter([self::SIMPLE_PRODUCT_ID])->getItems();

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
