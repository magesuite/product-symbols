<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class RemoveDefaultStoreValues implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    protected \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(\Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->delete(
            $this->moduleDataSetup->getTable('symbol_to_product_index'),
            ['store_id = ?' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [\MageSuite\ProductSymbols\Setup\Patch\Data\InstallSymbolAttributes::class];
    }
}
