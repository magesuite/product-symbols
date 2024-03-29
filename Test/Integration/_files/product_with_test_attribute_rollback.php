<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$registry = $objectManager->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$product = $productRepository->get('simple');
if ($product->getId()) {
    $product->delete();
}

$attribute = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
$attribute->loadByCode(\Magento\Catalog\Model\Product::ENTITY, 'meta_description');
$attribute->setIsUsedForPromoRules(0);
$attribute->save();
