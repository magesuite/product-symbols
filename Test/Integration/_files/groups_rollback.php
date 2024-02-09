<?php
$attributeRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(\Magento\Catalog\Model\Product\Attribute\Repository::class);

foreach(['first_group', 'second_group', 'third_group'] as $attributeCode) {
    try {
        $attribute = $attributeRepository->get($attributeCode);

        if ($attribute->getId()) {
            $attribute->delete();
        }
    } catch (\Exception $e) {
    }
}
