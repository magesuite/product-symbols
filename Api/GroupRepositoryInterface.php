<?php
namespace MageSuite\ProductSymbols\Api;

interface GroupRepositoryInterface
{
    /**
     * @param int $id
     * @return \MageSuite\ProductSymbols\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupInterface $group
     * @return \MageSuite\ProductSymbols\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(\MageSuite\ProductSymbols\Api\Data\GroupInterface $group);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \MageSuite\ProductSymbols\Api\Data\GroupInterface $group
     * @return void
     */
    public function delete(\MageSuite\ProductSymbols\Api\Data\GroupInterface $group);

    /**
     * @param $groupCode
     * @return \Magento\Framework\DataObject|null
     */
    public function getByCode($groupCode);
}
