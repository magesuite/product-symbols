<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer;

class SymbolName extends \MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer\AbstractColumnRenderer
{
    /**
     * @param $columnId
     * @param $entityId
     * @return mixed|string
     */
    public function getColumnValue($columnId, $entityId)
    {
        $symbolData = $this->getSymbolData($entityId);

        if (!$symbolData->getEntityId()) {
            return '';
        }

        return $symbolData->getSymbolName();
    }
}
