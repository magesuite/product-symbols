<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Setup\Patch\Data;

class AddCmsBlockAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
            'cms_block_identifier',
            [
                'type' => 'varchar',
                'label' => 'Cms Block Identifier',
                'input' => 'select',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'source' => \Magento\Cms\Model\Config\Source\Block::class
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
