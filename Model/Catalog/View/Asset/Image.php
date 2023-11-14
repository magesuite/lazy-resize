<?php

namespace MageSuite\LazyResize\Model\Catalog\View\Asset;

class Image implements \Magento\Framework\View\Asset\LocalInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
     * @var string
     */
    protected $contentType = 'image';

    /**
     * Misc image params depend on size, transparency, quality, watermark etc.
     *
     * @var array
     */
    protected $miscParams;

    /**
     * @var \Magento\Catalog\Model\Product\Media\ConfigInterface
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $urlBuilder = null;

    /**
     * Image constructor.
     * @param \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig
     * @param \Magento\Framework\View\Asset\ContextInterface $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param $filePath
     * @param array $miscParams
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Media\ConfigInterface $mediaConfig,
        \Magento\Framework\View\Asset\ContextInterface $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
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
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $value = $this->mediaBaseUrl . $this->getImageInfo();

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->getContext()->getPath() . DIRECTORY_SEPARATOR . str_replace('catalog/product/', '', $this->getImageInfo());
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        return $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($this->getFilePath(), DIRECTORY_SEPARATOR);
    }

    /**
     * Get source content type
     *
     * @return string
     */
    public function getSourceContentType()
    {
        return $this->sourceContentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * {@inheritdoc}
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
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

        return $this->getUrlBuilder()->buildUrl($attributes);
    }

    protected function getAttributes()
    {
        $imageFile= $this->getFilePath();


        return [
            'image_file' => $imageFile,
            'type' => $this->getContentType(),
            'width' => $this->miscParams['image_width'],
            'height' => $this->miscParams['image_height'],
            'frame' => $this->miscParams['keep_frame'],
            'aspect_ratio' => $this->miscParams['keep_aspect_ratio'],
            'transparency' => $this->miscParams['keep_transparency'],
            'enable_optimization' => (boolean) $this->scopeConfig->getValue('images/images_optimization/enable_optimization'),
            'background' => $this->miscParams['background'],
            'optimization_level' => $this->scopeConfig->getValue('dev/images_optimization/images_optimization_level')
        ];
    }

    public function getUrlBuilder()
    {
        if($this->urlBuilder){
            return $this->urlBuilder;
        }

        return new \MageSuite\LazyResize\Service\UrlBuilder(new \MageSuite\LazyResize\Service\TokenGenerator());
    }

    public function setUrlBuilder($urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;

        return $this;
    }
}
