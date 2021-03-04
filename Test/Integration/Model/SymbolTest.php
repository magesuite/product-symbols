<?php

namespace MageSuite\ProductSymbols\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class SymbolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    private $symbolRepositoryInterface;

    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolFactory
     */
    private $symbolFactory;

    private $store;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->symbolRepositoryInterface = $this->objectManager->create(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);
        $this->symbolFactory = $this->objectManager->create(\MageSuite\ProductSymbols\Model\SymbolFactory::class);

        $this->store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testIsNewSymbolSavedCorrectlyToDb()
    {
        $symbol = $this->symbolRepositoryInterface->getById(600, 1);

        $this->assertEquals(1, $symbol->getStoreId());
        $this->assertEquals('test symbol 1', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 1', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/pub/media/symbol/testimage.png', $symbol->getSymbolIconUrl());
        $this->assertEquals('100,200', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());

        $symbol = $this->symbolRepositoryInterface->getById(700, 0);
        $this->assertEquals(0, $symbol->getStoreId());
        $this->assertEquals('test symbol 2', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 2', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/pub/media/symbol/testimage.png', $symbol->getSymbolIconUrl());
        $this->assertEquals('100,200', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());

        $symbol = $this->symbolRepositoryInterface->getById(800, 0);

        $this->assertEquals(0, $symbol->getStoreId());
        $this->assertEquals('test symbol 3', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 3', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/pub/media/symbol/testimage.png', $symbol->getSymbolIconUrl());
        $this->assertEquals('200', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());

        $store = $this->store->load('test333', 'code');

        $symbol = $this->symbolRepositoryInterface->getById(1000, $store->getId());

        $this->assertEquals($store->getId(), $symbol->getStoreId());
        $this->assertEquals('test symbol 4', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 4', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/pub/media/symbol/testimage.png', $symbol->getSymbolIconUrl());
        $this->assertEquals('200,300', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());

        $symbol = $this->symbolRepositoryInterface->getById(1100, $store->getId());
        $this->assertEquals($store->getId(), $symbol->getStoreId());
        $this->assertEquals('test symbol 5', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 5', $symbol->getSymbolShortDescription());
        $this->assertEquals('http://localhost/pub/media/symbol/testimage.png', $symbol->getSymbolIconUrl());
        $this->assertEquals('100,300', $symbol->getSymbolGroups());
        $this->assertEquals('testimage.png', $symbol->getSymbolIcon());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testIsEditedSymbolSavedCorrectlyToDb()
    {
        $editData = [
            'store_id' => 1,
            'symbol_name' => 'edit symbol2',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 2',
            'symbol_groups' => [100,200]
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
            'symbol_groups' => [100,200]
        ];

        $symbol = $this->symbolRepositoryInterface->getById(700, $editData['store_id']);
        $symbol
            ->setStoreId($editData['store_id'])
            ->setSymbolShortDescription($editData['symbol_short_description'])
            ->setSymbolIcon($editData['symbol_icon'])
            ->setSymbolName($editData['symbol_name'])
            ->setSymbolGroups($editData['symbol_groups']);

        $this->symbolRepositoryInterface->save($symbol);
        $editedSymbol = $this->symbolRepositoryInterface->getById(700, $editData['store_id']);

        $this->assertEquals($editData['symbol_name'], $editedSymbol->getSymbolName());
        $this->assertEquals($editData['store_id'], $editedSymbol->getStoreId());
        $this->assertEquals($editData['symbol_icon'], $editedSymbol->getSymbolIcon());
        $this->assertEquals($editData['symbol_short_description'], $editedSymbol->getSymbolShortDescription());

        $store = $this->store->load('test333', 'code');

        $editData = [
            'store_id' => $store->getId(),
            'symbol_name' => 'edit symbol 1000',
            'symbol_icon' => 'edit_image.jpg',
            'symbol_short_description' => 'symbol short description 1000',
            'symbol_groups' => [100,200]
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
    public function testDeleteSymbolFromDb()
    {

        $savedSymbol = $this->symbolRepositoryInterface->getById(600);

        $result = $this->symbolRepositoryInterface->delete($savedSymbol);

        $this->assertTrue($result);
    }

    public static function loadSymbols()
    {
        include __DIR__.'/../_files/symbols.php';
    }

    public static function loadSymbolsRollback()
    {
        include __DIR__.'/../_files/symbols_rollback.php';
    }
}
