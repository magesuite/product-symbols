<?php
require __DIR__ . '/store.php';
require __DIR__ . '/groups.php';

/** @var  \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository */
$symbolRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Api\SymbolRepositoryInterface');

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Symbol');
$symbol
    ->setEntityId(600)
    ->setStoreId(0)
    ->setSymbolName('test symbol 1')
    ->setSymbolShortDescription('this is test symbol 1')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('100,200');


$symbolRepository->save($symbol);

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Symbol');
$symbol
    ->setEntityId(700)
    ->setStoreId(0)
    ->setSymbolName('test symbol 2')
    ->setSymbolShortDescription('this is test symbol 2')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('100,200');

$symbolRepository->save($symbol);

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Symbol');
$symbol
    ->setEntityId(800)
    ->setStoreId(0)
    ->setSymbolName('test symbol 3')
    ->setSymbolShortDescription('this is test symbol 3')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('200');

$symbolRepository->save($symbol);

$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
$store->load('test333', 'code');

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Symbol');
$symbol
    ->setEntityId(1000)
    ->setStoreId($store->getId())
    ->setSymbolName('test symbol 4')
    ->setSymbolShortDescription('this is test symbol 4')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('200,300');

$symbolRepository->save($symbol);

/** @var  \MageSuite\ProductSymbols\Model\Symbol $symbol */
$symbol = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Symbol');
$symbol
    ->setEntityId(1100)
    ->setStoreId($store->getId())
    ->setSymbolName('test symbol 5')
    ->setSymbolShortDescription('this is test symbol 5')
    ->setSymbolIcon('testimage.png')
    ->setSymbolGroups('100,300');

$symbolRepository->save($symbol);
