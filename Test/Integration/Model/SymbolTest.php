<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class SymbolTest extends \PHPUnit\Framework\TestCase
{
    protected const SYMBOL_WITH_CONDITION = 1101;

    protected ?\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepositoryInterface;
    protected ?\MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory;
    protected ?\Magento\Store\Model\Store $store;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->symbolRepositoryInterface = $objectManager->get(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);
        $this->symbolFactory = $objectManager->get(\MageSuite\ProductSymbols\Model\SymbolFactory::class);
        $this->store = $objectManager->get(\Magento\Store\Model\Store::class);
        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testIsNewSymbolSavedCorrectly(): void
    {
        $symbol = $this->symbolRepositoryInterface->getById(600, 1);
        $url = str_replace('pub/', '', $symbol->getSymbolIconUrl());
        $this->assertEquals(1, $symbol->getStoreId());

        $this->assertEquals('test symbol 1', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 1', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/media/symbol/testimage.png', $url);
        $this->assertEquals('100,200', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());
        $this->assertEquals(10, $symbol->getSortOrder());

        $store = $this->store->load('test333', 'code');

        $symbol = $this->symbolRepositoryInterface->getById(1000, $store->getId());
        $url = str_replace('pub/', '', $symbol->getSymbolIconUrl());

        $this->assertEquals($store->getId(), $symbol->getStoreId());
        $this->assertEquals('test symbol 4', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 4', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/media/symbol/testimage.png', $url);
        $this->assertEquals('200,300', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());
        $this->assertEquals(20, $symbol->getSortOrder());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testIsEditedSymbolSavedCorrectly(): void
    {
        $editData = [
            'store_id' => 1,
            'symbol_name' => 'edit symbol2',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 2',
            'symbol_groups' => [100, 200],
            'cms_block_identifier' => 'header_topbar_hotline',
            'sort_order' => 100
        ];

        $symbol = $this->symbolRepositoryInterface->getById(600, $editData['store_id']);
        $symbol
            ->setStoreId($editData['store_id'])
            ->setSymbolShortDescription($editData['symbol_short_description'])
            ->setSymbolIcon($editData['symbol_icon'])
            ->setSymbolName($editData['symbol_name'])
            ->setSymbolGroups($editData['symbol_groups'])
            ->setCmsBlockIdentifier($editData['cms_block_identifier'])
            ->setSortOrder($editData['sort_order']);

        $this->symbolRepositoryInterface->save($symbol);
        $editedSymbol = $this->symbolRepositoryInterface->getById(600, $editData['store_id']);

        $this->assertEquals($editData['symbol_name'], $editedSymbol->getSymbolName());
        $this->assertEquals($editData['store_id'], $editedSymbol->getStoreId());
        $this->assertEquals($editData['symbol_icon'], $editedSymbol->getSymbolIcon());
        $this->assertEquals($editData['symbol_short_description'], $editedSymbol->getSymbolShortDescription());
        $this->assertEquals($editData['cms_block_identifier'], $editedSymbol->getCmsBlockIdentifier());
        $this->assertEquals($editData['sort_order'], $editedSymbol->getSortOrder());

        $editData = [
            'store_id' => 0,
            'symbol_name' => 'edit symbol 700',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 700',
            'symbol_groups' => [100, 200],
            'sort_order' => 1000
        ];

        $symbol = $this->symbolRepositoryInterface->getById(1000, $editData['store_id']);
        $symbol
            ->setStoreId($editData['store_id'])
            ->setSymbolShortDescription($editData['symbol_short_description'])
            ->setSymbolIcon($editData['symbol_icon'])
            ->setSymbolName($editData['symbol_name'])
            ->setSymbolGroups($editData['symbol_groups'])
            ->setSortOrder($editData['sort_order']);

        $this->symbolRepositoryInterface->save($symbol);
        $editedSymbol = $this->symbolRepositoryInterface->getById(1000, $editData['store_id']);

        $this->assertEquals($editData['symbol_name'], $editedSymbol->getSymbolName());
        $this->assertEquals($editData['store_id'], $editedSymbol->getStoreId());
        $this->assertEquals($editData['symbol_icon'], $editedSymbol->getSymbolIcon());
        $this->assertEquals($editData['symbol_short_description'], $editedSymbol->getSymbolShortDescription());
        $this->assertEquals($editData['sort_order'], $editedSymbol->getSortOrder());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testIsSymbolDeleted(): void
    {
        $symbol = $this->symbolRepositoryInterface->getById(600);
        $result = $this->symbolRepositoryInterface->delete($symbol);

        $this->assertTrue($result);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     *
     */
    public function testItValidateRulesCorrectly(): void
    {
        $symbol = $this->symbolRepositoryInterface->getById(self::SYMBOL_WITH_CONDITION);
        $product = $this->productRepository->get('simple');

        $this->assertFalse($symbol->validate($product));

        $product->setMetaDescription('meta_description_symbol_match');
        $this->assertTrue(($symbol->validate($product)));
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testItReturnCorrectFlagForSvgImage(): void
    {
        $testSymbolId = 600;
        $symbol = $this->symbolRepositoryInterface->getById($testSymbolId);

        $pngImageName = 'image.png';
        $svgImageName = 'image.svg';

        $this->assertFalse($symbol->shouldDisplaySvgInline($pngImageName));
        $this->assertTrue($symbol->shouldDisplaySvgInline($svgImageName));
    }
}
