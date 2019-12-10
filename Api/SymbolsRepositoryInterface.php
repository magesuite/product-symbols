<?php
namespace MageSuite\ProductSymbols\Api;

interface SymbolsRepositoryInterface
{
    /**
     * @param int $id
     * @param int|null $storeId
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id, $storeId = null);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsInterface
     */
    public function save(\MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol
     * @return void
     */
    public function delete(\MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol);

    /**
     * @param int|string $storeId
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsInterface[]
     */
    public function getAllSymbols($storeId);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsInterface
     */
    public function create(\MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsInterface
     */
    public function update(\MageSuite\ProductSymbols\Api\Data\SymbolsInterface $symbol);

    /**
     * @param int|string $id
     * @return bool
     */
    public function deleteById($id);

}