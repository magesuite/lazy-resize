<?php

namespace MageSuite\LazyResize\Model\Catalog\View\Asset;

class Image implements \Magento\Framework\View\Asset\LocalInterface
{
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig;
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;
    protected \Magento\Framework\View\Asset\ContextInterface $context;
    protected \MageSuite\LazyResize\Model\FileSizeRepository $fileSizeRepository;
    protected \MageSuite\LazyResize\Helper\Configuration $configuration;
    protected \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler;
    protected string $contentType = 'image';
    protected $urlBuilder = null;
    protected static array $urlCache;
    protected string $mediaBaseUrl;

    /**
     * Image type of image (thumbnail,small_image,image,swatch_image,swatch_thumb)
     */
    protected $sourceContentType;
    protected \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory;
    protected string $filePath;
    protected array $miscParams;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig,
        \Magento\Framework\View\Asset\ContextInterface $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\LazyResize\Model\FileSizeRepository $fileSizeRepository,
        \MageSuite\LazyResize\Helper\Configuration $configuration,
        \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler,
        \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory,
        $filePath,
        array $miscParams
    ) {
        if (isset($miscParams['image_type'])) {
            $this->sourceContentType = $miscParams['image_type'];
            unset($miscParams['image_type']);
        } else {
            $this->sourceContentType = $this->contentType;
        }
        $this->mediaConfig = $mediaConfig;
        $this->context = $context;
        $this->filePath = $filePath;
        $this->miscParams = $miscParams;
        $this->storeManager = $storeManager;

        $this->mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->fileSizeRepository = $fileSizeRepository;
        $this->configuration = $configuration;
        $this->imageUrlHandler = $imageUrlHandler;
        $this->watermarkConfigurationFactory = $watermarkConfigurationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): string
    {
        $value = $this->mediaBaseUrl . $this->getImageInfo();

        return $value;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getPath(): string
    {
        return $this->getContext()->getPath() . DIRECTORY_SEPARATOR . str_replace('catalog/product/', '', $this->getImageInfo());
    }

    public function getSourceFile(): string
    {
        return $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($this->getFilePath(), DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getSourceContentType()
    {
        return $this->sourceContentType;
    }

    public function getContent()
    {
        return null;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getContext(): \Magento\Framework\View\Asset\ContextInterface
    {
        return $this->context;
    }

    public function getModule(): string
    {
        return 'cache';
    }

    /**
     * Generate path from image info
     *
     * @return string
     */
    public function getImageInfo()
    {
        $attributes = $this->getAttributes();
        $attributeHash = md5(serialize($attributes)); // phpcs:ignore

        return self::$urlCache[$attributeHash] ??= $this->getUrlBuilder()->generateUrl($attributes);
    }

    protected function getAttributes(): array
    {
        $imageFile = $this->getFilePath();

        return [
            'image_file' => $imageFile,
            'file_size' => $this->fileSizeRepository->getFileSize($imageFile),
            'type' => $this->getContentType(),
            'width' => (int)$this->miscParams['image_width'],
            'height' => (int)$this->miscParams['image_height'],
            'frame' => $this->miscParams['keep_frame'],
            'aspect_ratio' => $this->returnFormattedStringValue($this->miscParams['keep_aspect_ratio']),
            'transparency' => $this->returnFormattedStringValue($this->miscParams['keep_transparency']),
            'enable_optimization' => $this->returnFormattedStringValue($this->configuration->isOptimizationEnabled()),
            'background' => $this->miscParams['background'],
            'optimization_level' => $this->configuration->getOptimizationLevel(),
            'watermark' => $this->getWatermarkConfiguration()
        ];
    }

    protected function getWatermarkConfiguration(): \MageSuite\ImageResize\Model\WatermarkConfiguration
    {
        $watermark = $this->watermarkConfigurationFactory->create();
        $watermark->setImage($this->miscParams['watermark_file'] ?? null)
            ->setPosition($this->miscParams['watermark_position'] ?? null)
            ->setOpacity($this->miscParams['watermark_image_opacity'] ?? null)
            ->setWidth($this->miscParams['watermark_width'] ?? null)
            ->setHeight($this->miscParams['watermark_height'] ?? null);

        return $watermark;
    }

    public function getUrlBuilder()
    {
        if ($this->urlBuilder) {
            return $this->urlBuilder;
        }

        return $this->imageUrlHandler;
    }

    public function setUrlBuilder($urlBuilder): self
    {
        $this->urlBuilder = $urlBuilder;

        return $this;
    }

    private function returnFormattedStringValue($value): string
    {
        return $value ? '1' : '0';
    }
}
