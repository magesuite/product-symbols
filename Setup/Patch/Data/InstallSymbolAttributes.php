<?php
namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class InstallSymbolAttributes implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var \MageSuite\ProductSymbols\Setup\SymbolSetupFactory
     */
    protected $symbolSetupFactory;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \MageSuite\ProductSymbols\Setup\SymbolSetupFactory $symbolSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->symbolSetupFactory = $symbolSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $symbolSetup = $this->symbolSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $symbolSetup->installEntities();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
