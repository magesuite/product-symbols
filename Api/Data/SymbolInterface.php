<?php

namespace MageSuite\ProductSymbols\Api\Data;

interface SymbolInterface
{
    /**
     * @return int|string|null
     */
    public function getEntityId();

    /**
     * @param int|string|null $entityId
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return int|string|null
     */
    public function getCreatedAt();

    /**
     * @param int|string|null $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * @return int|string|null
     */
    public function getUpdatedAt();

    /**
     * @param int|string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getSymbolName();

    /**
     * @param string $symbolName
     * @return void
     */
    public function setSymbolName($symbolName);

    /**
     * @return mixed
     */
    public function getSymbolIcon();

    /**
     * @param $symbolIcon
     * @return mixed
     */
    public function setSymbolIcon($symbolIcon);

    /**
     * @return mixed
     */
    public function getSymbolShortDescription();

    /**
     * @param string|null $symbolShortDescription
     * @return mixed
     */
    public function setSymbolShortDescription($symbolShortDescription);

    /**
     * @return mixed
     */
    public function getSymbolDescription();

    /**
     * @param string|null $symbolDescription
     * @return mixed
     */
    public function setSymbolDescription($symbolDescription);

    /**
     * @return mixed
     */
    public function getSymbolGroups();

    /**
     * @param $symbolGroups
     * @return mixed
     */
    public function setSymbolGroups($symbolGroups);

    /**
     * @return string
     */
    public function getSymbolIconUrl();

    /**
     * @param $symbolIconUrl
     * @return mixed
     */
    public function setSymbolIconUrl($symbolIconUrl);
    
    /**
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface
     */
    public function getSymbolIconEncodedData();

    /**
     * @param @param \MageSuite\ProductSymbols\Api\Data\SymbolImageInterface $symbolIcon
     * @return mixed
     */
    public function setSymbolIconEncodedData($symbolIcon);
}
