<?php

namespace MageSuite\ProductSymbols\Api\Data;

interface GroupInterface
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
    public function getGroupName();

    /**
     * @param string $groupName
     * @return void
     */
    public function setGroupName($groupName);

    /**
     * @return string
     */
    public function getGroupCode();

    /**
     * @param string $groupCode
     * @return void
     */
    public function setGroupCode($groupCode);
    
}