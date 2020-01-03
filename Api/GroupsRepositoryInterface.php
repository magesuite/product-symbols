<?php
namespace MageSuite\ProductSymbols\Api;

interface GroupsRepositoryInterface
{
    /**
     * @param int $id
     * @return \MageSuite\ProductSymbols\Api\Data\GroupsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupsInterface $group
     * @return \MageSuite\ProductSymbols\Api\Data\GroupsInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(\MageSuite\ProductSymbols\Api\Data\GroupsInterface $group);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupsInterface $group
     * @return void
     */
    public function delete(\MageSuite\ProductSymbols\Api\Data\GroupsInterface $group);

    /**
     * @param $groupCode
     * @return \Magento\Framework\DataObject|null
     */
    public function getByCode($groupCode);
}