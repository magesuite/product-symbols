<?php
namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class AddSymbolDescriptionAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
        $setup = $this->moduleDataSetup->getConnection()->startSetup();

        $symbolSetup = $this->symbolSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $symbolSetup->addAttribute(
            \MageSuite\ProductSymbols\Model\Symbol::ENTITY,
            'symbol_description',
            [
                'type' => 'text',
                'label' => 'Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 8,
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