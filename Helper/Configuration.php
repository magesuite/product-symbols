<?php

namespace MageSuite\ProductSymbols\Helper;

class Configuration
{
    protected const XML_PATH_PRODUCT_SYMBOLS_SHOULD_DISPLAY_SVG_INLINE = 'product_symbols/general/should_display_svg_inline';
    protected const XML_PATH_PRODUCT_SYMBOLS_INDEXING_IS_ENABLED = 'product_symbols/general/indexing_is_enabled';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function shouldDisplaySvgInline(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PRODUCT_SYMBOLS_SHOULD_DISPLAY_SVG_INLINE);
    }

    public function isIndexingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PRODUCT_SYMBOLS_INDEXING_IS_ENABLED);
    }
}
