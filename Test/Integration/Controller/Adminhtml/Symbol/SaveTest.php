<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Symbol;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected const CUSTOM_STORE_ID = 1;

    protected ?\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepositoryInterface;

    protected function setUp(): void
    {
        parent::setUp();
        $this->symbolRepositoryInterface = $this->_objectManager->get(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testSaveSymbolWithoutConfig(): void
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
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testSaveSymbolWithoutConfigDifferentStore(): void
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
     * @magentoDataFixture MageSuite_ProductSymbols::Test/Integration/_files/symbols.php
     */
    public function testSaveSymbolWithConfigDifferentStore(): void
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

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveNewSymbolWithoutConfig(): void
    {
        $editData = [
            'store_id' => self::CUSTOM_STORE_ID,
            'symbol_name' => 'New symbol store 1',
            'symbol_short_description' => 'This is new symbol store 1',
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

        $symbols = $this->symbolRepositoryInterface->getAllSymbols();
        $this->assertEquals(1, count($symbols));
        $this->assertEquals('New symbol store 1', array_first($symbols)->getSymbolName());
    }
}
