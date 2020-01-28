<?php
namespace MageSuite\ProductSymbols\Api;

interface GroupToSymbolRelationRepositoryInterface
{
    /**
     * @param int $id
     * @return \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllByGroupId($id);
    /**
     * @param int $id
     * @return \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllBySymbolId($id);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface $relation
     * @return \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(\MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface $relation);

    /**
     * @param int $id
     * @return void
     */
    public function deleteBySymbolId($id);
}
