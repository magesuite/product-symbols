<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer;

class IsEnabled extends \MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer\AbstractColumnRenderer
{

    public function getColumnValue($columnId, $entityId)
    {
        $symbolData = $this->getSymbolData($entityId);

        if (!$symbolData->getEntityId()) {
            return '';
        }

        return $symbolData->getIsEnabled() ? __('Yes') : __(' No');
    }
}
