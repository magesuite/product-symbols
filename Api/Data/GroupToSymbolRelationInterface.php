<?php

namespace MageSuite\ProductSymbols\Api\Data;

interface GroupToSymbolRelationInterface
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
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $groupId
     * @return void
     */
    public function setGroupId($groupId);

    /**
     * @return int
     */
    public function getSymbolId();

    /**
     * @param int $symbolId
     * @return void
     */
    public function setSymbolId($symbolId);
}
