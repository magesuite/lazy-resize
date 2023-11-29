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

    /**
     * @var string
     */
    protected $mediaBaseUrl;

    /**
     * Image type of image (thumbnail,small_image,image,swatch_image,swatch_thumb)
     *
     * @var string
     */
    protected $sourceContentType;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * Misc image params depend on size, transparency, quality, watermark etc.
     */
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
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $value = $this->mediaBaseUrl . $this->getImageInfo();

        return $value;
    }

    public function getContentType()
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
        $attributeHash = md5(serialize($attributes));

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
            'optimization_level' => $this->configuration->getOptimizationLevel()
        ];
    }

    public function getUrlBuilder()
    {
        if ($this->urlBuilder) {
            return $this->urlBuilder;
        }

        return $this->imageUrlHandler;
    }

    public function setUrlBuilder($urlBuilder): static
    {
        $this->urlBuilder = $urlBuilder;

        return $this;
    }

    private function returnFormattedStringValue($value): string
    {
        return $value ? '1' : '0';
    }
}
