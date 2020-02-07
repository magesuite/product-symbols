<?php
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');

if ($store->load('test333', 'code')->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

$objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->reinitStores();
