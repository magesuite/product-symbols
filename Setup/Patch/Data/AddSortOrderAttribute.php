<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class AddSortOrderAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    protected \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup;

    protected \MageSuite\ProductSymbols\Setup\SymbolSetupFactory $symbolSetupFactory;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface  $moduleDataSetup,
        \MageSuite\ProductSymbols\Setup\SymbolSetupFactory $symbolSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->symbolSetupFactory = $symbolSetupFactory;
    }

    public function apply(): self
    {
        $setup = $this->moduleDataSetup->getConnection()->startSetup();
        $symbolSetup = $this->symbolSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $symbolSetup->addAttribute(
            \MageSuite\ProductSymbols\Model\Symbol::ENTITY,
            'sort_order',
            [
                'type' => 'int',
                'label' => 'Sort Order',
                'input' => 'text',
                'required' => false,
                'default' => 0,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
            ]
        );
        $setup->endSetup();

        return $this;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [
            \MageSuite\ProductSymbols\Setup\Patch\Data\InstallSymbolAttributes::class
        ];
    }
}
