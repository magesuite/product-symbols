<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer;

class GroupName extends \MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer\AbstractColumnRenderer
{
    /**
     * @param $columnId
     * @param $entityId
     * @return mixed|string
     */
    public function getColumnValue($columnId, $entityId)
    {
        $symbolData = $this->getGroupData($entityId);

        if (!$symbolData->getEntityId()) {
            return '';
        }

        return $symbolData->getGroupName();
    }
}
