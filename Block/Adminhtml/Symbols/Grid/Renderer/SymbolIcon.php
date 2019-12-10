<?php

namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbols\Grid\Renderer;

class SymbolIcon extends \MageSuite\ProductSymbols\Block\Adminhtml\Symbols\Grid\Renderer\AbstractColumnRenderer
{
    /**
     * @param $columnId
     * @param $entityId
     * @return mixed|string
     */
    public function getColumnValue($columnId, $entityId)
    {
        $symbolData = $this->getSymbolData($entityId);

        if (!$symbolData->getSymbolIcon()) {
            return '';
        }

        $iconPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() . 'symbols/' . $symbolData->getSymbolIcon();

        if (!file_exists($iconPath)) {
            return '';
        }
        return sprintf('<img src="%s" width="250"/>', $symbolData->getSymbolIconUrl());
    }
}