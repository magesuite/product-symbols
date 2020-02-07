<?php
namespace MageSuite\ProductSymbols\Api;

interface SymbolRepositoryInterface
{
    /**
     * @param int $id
     * @param int|null $storeId
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id, $storeId = null);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolInterface
     */
    public function save(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol
     * @return void
     */
    public function delete(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol);

    /**
     * @param int|string $storeId
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolInterface[]
     */
    public function getAllSymbols($storeId);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolInterface
     */
    public function create(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolInterface
     */
    public function update(\MageSuite\ProductSymbols\Api\Data\SymbolInterface $symbol);

    /**
     * @param int|string $id
     * @return bool
     */
    public function deleteById($id);
}
