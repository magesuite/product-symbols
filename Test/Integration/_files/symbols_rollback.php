<?php

/** @var \Magento\Framework\Registry $registry */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$registry = $objectManager->get('Magento\Framework\Registry');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$symbolRepository = $objectManager->create('MageSuite\ProductSymbols\Api\SymbolRepositoryInterface');

foreach ([600,700,800,1000,1100] as $symbolId) {
    $symbol = $objectManager->create('MageSuite\ProductSymbols\Model\Symbol');

    $symbol->load($symbolId);

    if ($symbol->getId() > 0) {
        $symbol->delete();
    }
}
