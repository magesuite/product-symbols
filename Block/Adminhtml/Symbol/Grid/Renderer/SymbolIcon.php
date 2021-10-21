<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer;

class SymbolIcon extends \MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer\AbstractColumnRenderer
{
    public function getColumnValue($columnId, $entityId)
    {
        $symbolData = $this->getSymbolData($entityId);

        if (!$symbolData->getSymbolIcon()) {
            return '';
        }

        $iconPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() . 'symbol/' . $symbolData->getSymbolIcon();

        if (!file_exists($iconPath)) { //phpcs:ignore
            return '';
        }

        return sprintf('<img src="%s" width="250"/>', $symbolData->getSymbolIconUrl());
    }
}
