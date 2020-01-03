<?php

namespace MageSuite\ProductSymbols\Api\Data;

interface SymbolsInterface
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
     * @return \MageSuite\ProductSymbols\Api\Data\SymbolsImagesInterface
     */
    public function getSymbolIconEncodedData();

    /**
     * @param @param \MageSuite\ProductSymbols\Api\Data\SymbolsImagesInterface $symbolIcon
     * @return mixed
     */
    public function setSymbolIconEncodedData($symbolIcon);
    
}