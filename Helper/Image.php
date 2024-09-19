<?php

namespace MageSuite\LazyResize\Helper;

class Image extends \Magento\Catalog\Helper\Image
{
    const NOT_SELECTED_IMAGE = 'no_selection';

    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \MageSuite\LazyResize\Model\FileSizeRepository $fileSizeRepository;
    protected \MageSuite\LazyResize\Helper\Configuration $configuration;
    protected \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\LazyResize\Api\FileSizeRepositoryInterface $fileSizeRepository,
        \MageSuite\LazyResize\Helper\Configuration $configuration,
        \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory
    ) {
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig);

        $this->storeManager = $storeManager;
        $this->fileSizeRepository = $fileSizeRepository;
        $this->configuration = $configuration;
        $this->watermarkConfigurationFactory = $watermarkConfigurationFactory;
    }

    public function getUrlBuilder(): \MageSuite\LazyResize\Service\ImageUrlHandler
    {
        return new \MageSuite\LazyResize\Service\ImageUrlHandler();
    }

    public function getUrl(): string
    {
        $attributes = $this->getAttributes();

        if ($attributes['image_file'] == null || $attributes['image_file'] == self::NOT_SELECTED_IMAGE) {
            return $this->getPlaceholderUrl();
        }

        try {
            return $this->getMediaBaseUrl() . $this->getUrlBuilder()->generateUrl($attributes);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

        return $this->getPlaceholderUrl();
    }

    public function getResizedImageInfo(): array
    {
        return [
            $this->getWidth(),
            $this->getHeight()
        ];
    }

    protected function getAttributes(): array
    {
        $imageFile = $this->getImageFile();

        if (!$imageFile) {
            $imageFile = $this->getProduct()->getData($this->_getModel()->getDestinationSubdir());
        }

        return [
            'image_file' => $imageFile,
            'type' => $this->getType(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'file_size' => $this->fileSizeRepository->getFileSize($imageFile),
            'frame' => $this->getFrame(),
            'aspect_ratio' => $this->returnFormattedStringValue($this->getAttribute('aspect_ratio')),
            'transparency' => $this->returnFormattedStringValue($this->getAttribute('transparency')),
            'enable_optimization' => $this->returnFormattedStringValue($this->configuration->isOptimizationEnabled()),
            'background' => $this->getAttribute('background'),
            'optimization_level' => $this->configuration->getOptimizationLevel(),
            'watermark' => $this->getWatermarkConfiguration()
        ];
    }

    protected function getWatermarkConfiguration(): ?\MageSuite\ImageResize\Model\WatermarkConfiguration
    {
        if (!$this->getWatermarkSize()) {
            return null;
        }

        $watermark = $this->watermarkConfigurationFactory->create();
        $watermark->setImage($this->getWatermark())
            ->setPosition($this->getWatermarkPosition())
            ->setOpacity($this->getWatermarkImageOpacity())
            ->setSize($this->getWatermarkSize());

        return $watermark;
    }

    protected function getPlaceholderUrl(): string
    {
        $destinationSubDir = $this->_getModel()->getDestinationSubdir();

        $placeholderPathFromConfig = $this->scopeConfig->getValue(
            "catalog/placeholder/{$destinationSubDir}_placeholder",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$placeholderPathFromConfig) {
            return $this->_assetRepo->getUrl("Magento_Catalog::images/product/placeholder/{$destinationSubDir}.jpg");
        }

        return $this->getMediaBaseUrl() . 'catalog/product/placeholder/' . $placeholderPathFromConfig;
    }

    protected function applyScheduledActions(): self
    {
        return $this;
    }

    private function returnFormattedStringValue($value): string
    {
        return $value ? '1' : '0';
    }

    protected function getMediaBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
