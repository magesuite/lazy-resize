<?php
declare(strict_types=1);

namespace MageSuite\LazyResize\Plugin\Magento\Catalog\Model\Product\Image\ParamsBuilder;

class AddWatermarkOffsetParams
{
    protected \MageSuite\LazyResize\Helper\Configuration\DesignConfiguration $designConfiguration;

    public function __construct(
        \MageSuite\LazyResize\Helper\Configuration\DesignConfiguration $designConfiguration
    ) {
        $this->designConfiguration = $designConfiguration;
    }

    public function afterBuild(\Magento\Catalog\Model\Product\Image\ParamsBuilder $subject, array $result, array $imageArguments, int $scopeId = null): array
    {
        $imageType = $result['image_type'] ?? null;

        if ($imageType) {
            $result['watermark_offset_x'] = $this->designConfiguration->getWatermarkOffsetX($imageType, $scopeId);
            $result['watermark_offset_y'] = $this->designConfiguration->getWatermarkOffsetY($imageType, $scopeId);
        }
        return $result;
    }
}
