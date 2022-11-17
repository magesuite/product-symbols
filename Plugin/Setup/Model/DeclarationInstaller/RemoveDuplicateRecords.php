<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\Plugin\Setup\Model\DeclarationInstaller;

class RemoveDuplicateRecords
{
    protected \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Remove duplicates before adding unique key for fields: attribute_id, store_id, entity_id
     *
     * @param \Magento\Setup\Model\DeclarationInstaller $subject
     * @param array $requestData
     * @return array[]
     */
    public function beforeInstallSchema(\Magento\Setup\Model\DeclarationInstaller $subject, array $requestData)
    {
        if ($this->isUniqueKeyAlreadyInstalled()) {
            return [$requestData];
        }

        $connection = $this->resourceConnection->getConnection();

        foreach ($this->getTableNames() as $tableName) {
            $select = $connection->select()
                ->from(['t1' => $tableName])
                ->join(['t2' => $tableName], '')
                ->where('t1.value_id < t2.value_id')
                ->where('t1.attribute_id = t2.attribute_id')
                ->where('t1.store_id = t2.store_id')
                ->where('t1.entity_id = t2.entity_id');
            $connection->query($select->deleteFromSelect('t1'));
        }

        return [$requestData];
    }

    protected function isUniqueKeyAlreadyInstalled(): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $tableNames = $this->getTableNames();

        foreach ($tableNames as $tableName) {
            if ($connection->isTableExists($tableName)) {
                continue;
            }

            return true;
        }

        $indexName = 'PRODUCT_SYMBOL_ENTITY_INT_ENTITY_ID_ATTRIBUTE_ID_STORE_ID';
        $indexList = $connection->getIndexList($tableNames[0]);

        return array_key_exists($indexName, $indexList);
    }

    protected function getTableNames(): array
    {
        return [
            $this->resourceConnection->getTableName('product_symbol_entity_int'),
            $this->resourceConnection->getTableName('product_symbol_entity_varchar'),
            $this->resourceConnection->getTableName('product_symbol_entity_text')
        ];
    }
}
