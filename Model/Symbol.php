<?php

namespace MageSuite\ProductSymbols\Model;

class Symbol extends \Magento\Catalog\Model\AbstractModel implements \MageSuite\ProductSymbols\Api\Data\SymbolInterface
{

    const SYMBOL_URL = 'product_symbol';
    const SYMBOL_ATTRIBUTE_CODE = 'product_symbol';
    const ENTITY = 'product_symbol';
    const CACHE_TAG = 'product_symbol';

    const STORE_ID = 'store_id';

    protected $_eventPrefix = 'product_symbol';

    protected $_eventObject = 'product_symbol';

    protected $_cacheTag = self::CACHE_TAG;

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
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
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
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
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

        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'symbol/' . $icon;
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
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface
     */
    public function getSymbolIconEncodedData()
    {
        return $this->getData('symbol_icon_encoded_data');
    }

    /**
     * @param @param \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface $symbolIcon
     * @return mixed
     */
    public function setSymbolIconEncodedData($symbolIcon)
    {
        return $this->setData('symbol_icon_encoded_data', $symbolIcon);
    }
}
