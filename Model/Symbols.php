<?php

namespace MageSuite\ProductSymbols\Model;

class Symbols extends \Magento\Catalog\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\SymbolsInterface
{

    const SYMBOL_URL = 'symbols';
    const SYMBOL_ATTRIBUTE_CODE = 'symbols';
    const ENTITY = 'symbols';
    const CACHE_TAG = 'symbols';

    const STORE_ID = 'store_id';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'symbols';
    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'symbols';
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
        $this->_init('MageSuite\ProductSymbols\Model\ResourceModel\Symbols');
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
    public function getSymbolName()
    {
        return $this->getData('symbol_name');
    }

    /**
     * @param string $symbolName
     * @return $this
     */
    public function setSymbolName($symbolName)
    {
        return $this->setData('symbol_name', $symbolName);
    }


    /**
     * @return mixed
     */
    public function getSymbolIcon()
    {
        return $this->getData('symbol_icon');
    }

    /**
     * @param $symbolIcon
     * @return mixed
     */
    public function setSymbolIcon($symbolIcon)
    {
        return $this->setData('symbol_icon', $symbolIcon);
    }


    /**
     * @return mixed
     */
    public function getSymbolShortDescription()
    {
        return $this->getData('symbol_short_description');
    }

    /**
     * @param $symbolShortDescription
     * @return mixed
     */
    public function setSymbolShortDescription($symbolShortDescription)
    {
        return $this->setData('symbol_short_description', $symbolShortDescription);
    }

    /**
     * @return mixed
     */
    public function getSymbolGroups()
    {
        return $this->getData('symbol_groups');
    }

    /**
     * @param $symbolGroups
     * @return mixed
     */
    public function setSymbolGroups($symbolGroups)
    {
        return $this->setData('symbol_groups', $symbolGroups);
    }


    /**
     * @param $image
     * @return string
     */
    public function getSymbolIconUrl($image = null)
    {
        if (!$icon = $this->getSymbolIcon()) {
            $icon = $image;
        }

        if (!$icon) {
            return '';
        }

        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'symbols/' . $icon;
    }

    /**
     * @param $symbolIconUrl
     * @return mixed
     */
    public function setSymbolIconUrl($symbolIconUrl)
    {
        return $this->setData('symbol_icon_url', $symbolIconUrl);
    }

    /**
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsImagesInterface
     */
    public function getSymbolIconEncodedData()
    {
        return $this->getData('symbol_icon_encoded_data');
    }

    /**
     * @param @param \MageSuite\ProductSymbols\Api\Data\SymbolsImagesInterface $symbolIcon
     * @return mixed
     */
    public function setSymbolIconEncodedData($symbolIcon)
    {
        return $this->setData('symbol_icon_encoded_data', $symbolIcon);
    }

}