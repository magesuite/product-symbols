<?php

namespace MageSuite\ProductSymbols\Model;

class Group extends \Magento\Catalog\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\GroupInterface
{

    const SYMBOL_URL = 'group';
    const SYMBOL_ATTRIBUTE_CODE = 'group';

    const ENTITY = 'group';
    const STORE_ID = 'store_id';

    const CACHE_TAG = 'groups';
    const EVENT_PREFIX = 'groups';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ProductSymbols\Model\ResourceModel\Group::class);
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
    public function getGroupName()
    {
        return $this->getData('group_name');
    }

    /**
     * @param string $groupName
     * @return $this
     */
    public function setGroupName($groupName)
    {
        return $this->setData('group_name', $groupName);
    }

    /**
     * @return int
     */
    public function getIgnoreProductAssignment()
    {
        return $this->getData('ignore_product_assignment');
    }

    /**
     * @param int $ignoreProductAssignment
     * @return $this
     */
    public function setIgnoreProductAssignment($ignoreProductAssignment)
    {
        return $this->setData('ignore_product_assignment', $ignoreProductAssignment);
    }

    /**
     * @return mixed
     */
    public function getGroupCode()
    {
        return $this->getData('group_code');
    }

    /**
     * @param string $groupCode
     * @return $this
     */
    public function setGroupCode($groupCode)
    {
        return $this->setData('group_code', $groupCode);
    }
}
