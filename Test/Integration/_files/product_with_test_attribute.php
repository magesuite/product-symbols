<?php

$fixtureResolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$fixtureResolver->requireDataFixture('Magento/Catalog/_files/product_simple.php');
$productRepository = $objectManager->create(\Magento\Catalog\Model\ProductRepository::class);
$product = $productRepository->get('simple');
$product->setMetaDescription('meta_description_symbol_match');
$product->save();
