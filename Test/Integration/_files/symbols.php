<?php
require __DIR__ . '/store.php';
require __DIR__ . '/groups.php';

/** @var  \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository */
$symbolRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\MageSuite\ProductSymbols\Api\SymbolRepositoryInterface::class);

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\MageSuite\ProductSymbols\Model\Symbol::class);
$symbol
    ->setEntityId(600)
    ->setStoreId(0)
    ->setSymbolName('test symbol 1')
    ->setSymbolShortDescription('this is test symbol 1')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('100,200')
    ->setSortOrder(10);

$symbolRepository->save($symbol);

$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
$store->load('test333', 'code');

$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\MageSuite\ProductSymbols\Model\Symbol::class);
$symbol
    ->setEntityId(1000)
    ->setStoreId($store->getId())
    ->setSymbolName('test symbol 4')
    ->setSymbolShortDescription('this is test symbol 4')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('200,300')
    ->setSortOrder(20);

$symbolRepository->save($symbol);

$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\MageSuite\ProductSymbols\Model\Symbol::class);
$symbol
    ->setEntityId(1101)
    ->setStoreId(0)
    ->setSymbolName('Symbol with conditions')
    ->setSymbolShortDescription('Symbol with conditions')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('100,300');

$symbol->loadPost(
    [
        'conditions' =>
            [
                '1' =>
                    [
                        'type' => 'Magento\\Rule\\Model\\Condition\\Combine',
                        'aggregator' => 'all',
                        'value' => '1',
                        'new_child' => '',
                    ],
                '1--1' =>
                    [
                        'type' => 'Magento\CatalogRule\Model\Rule\Condition\Product',
                        'attribute' => 'test_attribute',
                        'operator' => '==',
                        'value' => 'test_value'
                    ],
            ],
    ]
);

$symbolRepository->save($symbol);
