<?php

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Symbol;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    const CUSTOM_STORE_ID = 1;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepositoryInterface;

    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolFactory
     */
    protected $symbolFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->symbolRepositoryInterface = $this->_objectManager->create(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);
        $this->symbolFactory = $this->_objectManager->create(\MageSuite\ProductSymbols\Model\SymbolFactory::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testSaveSymbolWithoutConfig()
    {
        $editData = [
            'entity_id' => 600,
            'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            'symbol_name' => 'test symbol 2 edited',
            'symbol_short_description' => 'this is test symbol 2 edited',
            'symbol_icon' => [
                0 => [
                    'url' => '',
                    'name' => 'test_image.png'
                ]
            ],
            'use_config' => [
                'symbol_name' => 'false',
                'symbol_short_description' => 'false',
                'symbol_icon' => 'false'
            ]
        ];
        $this->getRequest()->setPostValue($editData);
        $this->dispatch('backend/symbol/symbol/save');

        $symbol = $this->symbolRepositoryInterface->getById(600, \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->assertEquals('test symbol 2 edited', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 2 edited', $symbol->getSymbolShortDescription());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testSaveSymbolWithoutConfigDifferentStore()
    {
        $editData = [
            'entity_id' => 600,
            'store_id' => self::CUSTOM_STORE_ID,
            'symbol_name' => 'test symbol 2 edited store 1',
            'symbol_short_description' => 'this is test symbol 2 edited store 1',
            'symbol_icon' => [
                0 => [
                    'url' => '',
                    'name' => 'test_image.png'
                ]
            ],
            'use_config' => [
                'symbol_name' => 'false',
                'symbol_short_description' => 'false',
                'symbol_icon' => 'false'
            ]
        ];
        $this->getRequest()->setPostValue($editData);
        $this->dispatch('backend/symbol/symbol/save');

        $symbol = $this->symbolRepositoryInterface->getById(600, self::CUSTOM_STORE_ID);

        $this->assertEquals('test symbol 2 edited store 1', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 2 edited store 1', $symbol->getSymbolShortDescription());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadSymbols
     */
    public function testSaveSymbolWithConfigDifferentStore()
    {
        $editData = [
            'entity_id' => 600,
            'store_id' => self::CUSTOM_STORE_ID,
            'symbol_name' => 'test symbol 2 edited store 1 used old value',
            'symbol_short_description' => 'this is test symbol 2 edited store 1 used new value',
            'symbol_icon' => [
                0 => [
                    'url' => '',
                    'name' => 'test_image.png'
                ]
            ],
            'use_config' => [
                'symbol_name' => 'true',
                'symbol_short_description' => 'false',
                'symbol_icon' => 'false'
            ]
        ];
        $this->getRequest()->setPostValue($editData);
        $this->dispatch('backend/symbol/symbol/save');

        $symbol = $this->symbolRepositoryInterface->getById(600, self::CUSTOM_STORE_ID);

        $this->assertEquals('test symbol 1', $symbol->getSymbolName());
        $this->assertEquals('this is test symbol 2 edited store 1 used new value', $symbol->getSymbolShortDescription());
    }

    public static function loadSymbols()
    {
        include __DIR__.'/../../../_files/symbols.php';
    }

    public static function loadSymbolsRollback()
    {
        include __DIR__.'/../../../_files/symbols_rollback.php';
    }
}
