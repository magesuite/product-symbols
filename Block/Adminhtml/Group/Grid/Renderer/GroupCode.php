<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer;

class GroupCode extends \MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer\AbstractColumnRenderer
{
    public function getColumnValue($columnId, $entityId)
    {
        $groupData = $this->getGroupData($entityId);

        if (!$groupData->getEntityId()) {
            return '';
        }

        return $groupData->getGroupCode();
    }
}
