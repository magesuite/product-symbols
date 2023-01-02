<?php

namespace MageSuite\ProductSymbols\Model;

class Symbol extends \Magento\Rule\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\SymbolInterface
{

    const SYMBOL_URL = 'product_symbol';
    const SYMBOL_ATTRIBUTE_CODE = 'product_symbol';
    const ENTITY = 'product_symbol';

    const STORE_ID = 'store_id';

    const CACHE_TAG = 'product_symbol';
    const EVENT_PREFIX = 'product_symbol';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    protected \MageSuite\ProductSymbols\Model\Symbol\Condition\CombineFactory $conditionsCombineFactory;

    protected \Magento\Rule\Model\Action\CollectionFactory $actionsFactory;

    protected \MageSuite\ProductSymbols\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductSymbols\Model\Symbol\Condition\CombineFactory $conditionsCombineFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionsFactory,
        \MageSuite\ProductSymbols\Helper\Configuration $configuration,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->storeManager = $storeManager;
        $this->conditionsCombineFactory = $conditionsCombineFactory;
        $this->actionsFactory = $actionsFactory;
        $this->configuration = $configuration;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\MageSuite\ProductSymbols\Model\ResourceModel\Symbol::class);
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
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * @return int|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * @param int|string|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * @return int|string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * @param int|string|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData('updated_at', $updatedAt);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * @return int
     */
    public function getIsEnabled()
    {
        return $this->getData('is_enabled');
    }

    /**
     * @param int $isEnabled
     *
     * @return $this
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData('is_enabled', $isEnabled);
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
     *
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
     *
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
     *
     * @return mixed
     */
    public function setSymbolShortDescription($symbolShortDescription)
    {
        return $this->setData('symbol_short_description', $symbolShortDescription);
    }

    /**
     * @return mixed
     */
    public function getSymbolDescription()
    {
        return $this->getData('symbol_description');
    }

    /**
     * @param $symbolDescription
     *
     * @return mixed
     */
    public function setSymbolDescription($symbolDescription)
    {
        return $this->setData('symbol_description', $symbolDescription);
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
     *
     * @return mixed
     */
    public function setSymbolGroups($symbolGroups)
    {
        return $this->setData('symbol_groups', $symbolGroups);
    }

    public function getCmsBlockIdentifier()
    {
        return $this->getData('cms_block_identifier');
    }

    public function setCmsBlockIdentifier($cmsBlockIdentifier)
    {
        return $this->setData('cms_block_identifier', $cmsBlockIdentifier);
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * @param string|null $image
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSymbolIconUrl($image = null)
    {
        if (!$icon = $this->getSymbolIcon()) {
            $icon = $image;
        }

        if (!$icon) {
            return '';
        }

        return $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'symbol/' . $icon;
    }

    /**
     * @param $symbolIconUrl
     *
     * @return mixed
     */
    public function setSymbolIconUrl($symbolIconUrl)
    {
        return $this->setData('symbol_icon_url', $symbolIconUrl);
    }

    /**
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface
     */
    public function getSymbolIconEncodedData()
    {
        return $this->getData('symbol_icon_encoded_data');
    }

    /**
     * @param @param \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface $symbolIcon
     *
     * @return mixed
     */
    public function setSymbolIconEncodedData($symbolIcon)
    {
        return $this->setData('symbol_icon_encoded_data', $symbolIcon);
    }

    public function getConditionsInstance()
    {
        return $this->conditionsCombineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->actionsFactory->create();
    }

    public function shouldDisplaySvgInline(string $image): bool
    {
        $extension = pathinfo($image, PATHINFO_EXTENSION); //phpcs:ignore

        if ($extension != self::SVG_FILE_EXTENSION) {
            return false;
        }

        return $this->configuration->shouldDisplaySvgInline();
    }
}
