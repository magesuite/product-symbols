<?php

namespace MageSuite\ProductSymbols\Model;

class GroupToSymbolRelation extends \Magento\Catalog\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\GroupToSymbolRelationInterface
{
    protected function _construct()
    {
        $this->_init(\MageSuite\ProductSymbols\Model\ResourceModel\GroupToSymbolRelation::class);
    }

    /**
     * @return int|string|null
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @param int|string|null $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->getData('group_id');
    }

    /**
     * @param int $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        return $this->setData('group_id', $groupId);
    }

    /**
     * @return mixed
     */
    public function getSymbolId()
    {
        return $this->getData('symbol_id');
    }

    /**
     * @param int $symbolId
     * @return $this
     */
    public function setSymbolId($symbolId)
    {
        return $this->setData('symbol_id', $symbolId);
    }
}
