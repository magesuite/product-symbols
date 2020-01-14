<?php

namespace MageSuite\ProductSymbols\Model;

class Group extends \Magento\Catalog\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\GroupInterface
{

    const SYMBOL_URL = 'group';
    const SYMBOL_ATTRIBUTE_CODE = 'group';
    const ENTITY = 'group';
    const CACHE_TAG = 'groups';

    const STORE_ID = 'store_id';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'groups';
    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'groups';
    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * Core data
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $storeManager, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('MageSuite\ProductSymbols\Model\ResourceModel\Group');
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