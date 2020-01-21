<?php

namespace MageSuite\ProductSymbols\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (!$setup->getConnection()->isTableExists($setup->getTable('symbols_entity'))) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('symbols_entity')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
                ->addColumn(
                    'symbol_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [],
                    'Symbol Name'
                )
                ->addColumn(
                    'symbol_short_description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [],
                    'Symbol Short Description'
                )
                ->addColumn(
                    'symbol_icon',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [],
                    'Symbol Icon'
                )
                ->addColumn(
                    'symbol_icon_url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [],
                    'Symbol Icon'
                )
                ->addColumn(
                    'symbol_groups',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Symbol Groups'
                )
                ->setComment(
                    'Symbols Entity'
                );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'eav_entity_int'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('symbols_entity_int')
            )->addColumn(
                'value_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Attribute Value'
            )->addIndex(
                $setup->getIdxName(
                    'symbols_entity_int',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_int', ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_int', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $setup->getFkName('symbols_entity_int', 'attribute_id', 'eav_attribute', 'attribute_id'),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('symbols_entity_int', 'entity_id', 'symbols_entity', 'entity_id'),
                    'entity_id',
                    $setup->getTable('symbols_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('symbols_entity_int', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment(
                    'Symbols Entity Value Prefix'
                );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'eav_entity_text'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('symbols_entity_text')
            )->addColumn(
                'value_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Attribute Value'
            )->addIndex(
                $setup->getIdxName(
                    'symbols_entity_text',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_text', ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_text', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'symbols_entity_text',
                        'attribute_id',
                        'eav_attribute',
                        'attribute_id'
                    ),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'symbols_entity_text',
                        'entity_id',
                        'symbols_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('symbols_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('symbols_entity_text', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment(
                    'Symbols Entity Value Prefix'
                );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'eav_entity_varchar'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('symbols_entity_varchar')
            )->addColumn(
                'value_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Attribute Value'
            )->addIndex(
                $setup->getIdxName(
                    'symbols_entity_varchar',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_varchar', ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $setup->getIdxName('symbols_entity_varchar', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'symbols_entity_varchar',
                        'attribute_id',
                        'eav_attribute',
                        'attribute_id'
                    ),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'symbols_entity_varchar',
                        'entity_id',
                        'symbols_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('symbols_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('symbols_entity_varchar', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment(
                    'Symbols Entity Value Prefix'
                );
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
