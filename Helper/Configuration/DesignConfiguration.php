<?php
declare(strict_types=1);

namespace MageSuite\LazyResize\Helper\Configuration;

class DesignConfiguration
{
    public const XML_PATH_PATTERN_WATERMARK_OFFSET_X = 'design/watermark/watermark_%s_imageOffsetX';
    public const XML_PATH_PATTERN_WATERMARK_OFFSET_Y = 'design/watermark/watermark_%s_imageOffsetY';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getWatermarkOffsetX(string $type, ?int $storeId = null): ?int
    {
        $value = $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_PATTERN_WATERMARK_OFFSET_X, $type),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? (int)$value : null;
    }

    public function getWatermarkOffsetY(string $type, ?int $storeId = null): ?int
    {
        $value = $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_PATTERN_WATERMARK_OFFSET_Y, $type),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value ? (int)$value : null;
    }
}
