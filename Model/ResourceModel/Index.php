<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel;

class Index
{
    protected ?\Magento\Framework\DB\Adapter\AdapterInterface $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function startTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function insert(array $data): int
    {
        return $this->connection->insertMultiple(
            $this->connection->getTableName('symbol_to_product_index'),
            $data
        );
    }

    public function deleteByProductId(array $toDeleteProductIds): void
    {
        $this->connection->delete(
            $this->connection->getTableName('symbol_to_product_index'),
            $this->connection->quoteInto('product_id IN (?)', $toDeleteProductIds)
        );
    }

    public function getByProductIds(array $productIds): ?array
    {
        $select = $this->connection->select();
        $select->from($this->connection->getTableName('symbol_to_product_index'));
        $select->where('product_id IN (?)', $productIds);

        $result = [];

        foreach ($this->connection->fetchAll($select) as $row) {
            $result[$row['product_id']][] = $row['symbol_id'];
        }

        return $result;
    }
}
