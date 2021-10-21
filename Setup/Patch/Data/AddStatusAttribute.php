<?php
namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class AddStatusAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const IS_ENABLED_ATTRIBUTE_CODE = 'is_enabled';

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
        $setup = $this->moduleDataSetup->getConnection()->startSetup();

        $symbolSetup = $this->symbolSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $symbolSetup->addAttribute(
            \MageSuite\ProductSymbols\Model\Symbol::ENTITY,
            self::IS_ENABLED_ATTRIBUTE_CODE,
            [
                'type' => 'int',
                'label' => 'Is enabled',
                'input' => 'select',
                'required' => false,
                'sort_order' => 0,
                'source_model' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ]
        );

        $setup->endSetup();
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [\MageSuite\ProductSymbols\Setup\Patch\Data\InstallSymbolAttributes::class];
    }
}
