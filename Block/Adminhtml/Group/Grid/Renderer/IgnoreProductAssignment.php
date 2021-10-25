<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer;

class IgnoreProductAssignment extends \MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer\AbstractColumnRenderer
{
    public function getColumnValue($columnId, $entityId)
    {
        $groupData = $this->getGroupData($entityId);

        if (!$groupData->getEntityId()) {
            return '';
        }

        return $groupData->getIgnoreProductAssignment() ? __('Yes') : __('No');
    }
}
