<?php

namespace MageSuite\ProductSymbols\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class SymbolTest extends \PHPUnit\Framework\TestCase
{
    const SYMBOL_WITH_CONDITION = 1101;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepositoryInterface;

    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolFactory
     */
    protected $symbolFactory;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->symbolRepositoryInterface = $this->objectManager->create(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);
        $this->symbolFactory = $this->objectManager->create(\MageSuite\ProductSymbols\Model\SymbolFactory::class);
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->productRepository = $this->objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testIsNewSymbolSavedCorrectly()
    {
        $symbol = $this->symbolRepositoryInterface->getById(600, 1);
        $url = str_replace('pub/', '', $symbol->getSymbolIconUrl());
        $this->assertEquals(1, $symbol->getStoreId());

        $this->assertEquals('test symbol 1', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 1', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/media/symbol/testimage.png', $url);
        $this->assertEquals('100,200', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());

        $store = $this->store->load('test333', 'code');

        $symbol = $this->symbolRepositoryInterface->getById(1000, $store->getId());
        $url = str_replace('pub/', '', $symbol->getSymbolIconUrl());

        $this->assertEquals($store->getId(), $symbol->getStoreId());
        $this->assertEquals('test symbol 4', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 4', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/media/symbol/testimage.png', $url);
        $this->assertEquals('200,300', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testIsEditedSymbolSavedCorrectly()
    {
        $editData = [
            'store_id' => 1,
            'symbol_name' => 'edit symbol2',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 2',
            'symbol_groups' => [100, 200]
        ];

        $symbol = $this->symbolRepositoryInterface->getById(600, $editData['store_id']);
        $symbol
            ->setStoreId($editData['store_id'])
            ->setSymbolShortDescription($editData['symbol_short_description'])
            ->setSymbolIcon($editData['symbol_icon'])
            ->setSymbolName($editData['symbol_name'])
            ->setSymbolGroups($editData['symbol_groups']);

        $this->symbolRepositoryInterface->save($symbol);
        $editedSymbol = $this->symbolRepositoryInterface->getById(600, $editData['store_id']);

        $this->assertEquals($editData['symbol_name'], $editedSymbol->getSymbolName());
        $this->assertEquals($editData['store_id'], $editedSymbol->getStoreId());
        $this->assertEquals($editData['symbol_icon'], $editedSymbol->getSymbolIcon());
        $this->assertEquals($editData['symbol_short_description'], $editedSymbol->getSymbolShortDescription());

        $editData = [
            'store_id' => 0,
            'symbol_name' => 'edit symbol 700',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 700',
            'symbol_groups' => [100, 200]
        ];

        $symbol = $this->symbolRepositoryInterface->getById(1000, $editData['store_id']);
        $symbol
            ->setStoreId($editData['store_id'])
            ->setSymbolShortDescription($editData['symbol_short_description'])
            ->setSymbolIcon($editData['symbol_icon'])
            ->setSymbolName($editData['symbol_name'])
            ->setSymbolGroups($editData['symbol_groups']);

        $this->symbolRepositoryInterface->save($symbol);
        $editedSymbol = $this->symbolRepositoryInterface->getById(1000, $editData['store_id']);

        $this->assertEquals($editData['symbol_name'], $editedSymbol->getSymbolName());
        $this->assertEquals($editData['store_id'], $editedSymbol->getStoreId());
        $this->assertEquals($editData['symbol_icon'], $editedSymbol->getSymbolIcon());
        $this->assertEquals($editData['symbol_short_description'], $editedSymbol->getSymbolShortDescription());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testIsSymbolDeleted()
    {
        $symbol = $this->symbolRepositoryInterface->getById(600);
        $result = $this->symbolRepositoryInterface->delete($symbol);

        $this->assertTrue($result);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture loadSymbols
     *
     */
    public function testItValidateRulesCorrectly()
    {
        $symbol = $this->symbolRepositoryInterface->getById(self::SYMBOL_WITH_CONDITION);
        $product = $this->productRepository->get('simple');

        $this->assertFalse($symbol->validate($product));

        $product->setTestAttribute('test_value');
        $this->assertTrue(($symbol->validate($product)));
    }

    public static function loadSymbols()
    {
        include __DIR__ . '/../_files/symbols.php';
    }

    public static function loadSymbolsRollback()
    {
        include __DIR__ . '/../_files/symbols_rollback.php';
    }
}
