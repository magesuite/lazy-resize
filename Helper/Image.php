<?php

namespace MageSuite\LazyResize\Helper;

class Image extends \Magento\Catalog\Helper\Image
{
    const NOT_SELECTED_IMAGE = 'no_selection';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $mediaBaseUrl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig);

        $this->storeManager = $storeManager;
        $this->mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getUrlBuilder()
    {
        return new \MageSuite\LazyResize\Service\ImageUrlHandler();
    }

    public function getUrl()
    {
        $attributes = $this->getAttributes();

        if ($attributes['image_file'] == null || $attributes['image_file'] == self::NOT_SELECTED_IMAGE) {
            return $this->getPlaceholderUrl();
        }

        return $this->mediaBaseUrl . $this->getUrlBuilder()->generateUrl($attributes);
    }

    public function getResizedImageInfo()
    {
        return [
            $this->getWidth(),
            $this->getHeight()
        ];
    }

    protected function getAttributes()
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
            'frame' => $this->getFrame(),
            'aspect_ratio' => $this->getAttribute('aspect_ratio'),
            'transparency' => $this->getAttribute('transparency'),
            'enable_optimization' => (boolean) $this->scopeConfig->getValue('images/images_optimization/enable_optimization'),
            'background' => $this->getAttribute('background'),
            'optimization_level' => $this->scopeConfig->getValue('dev/images_optimization/images_optimization_level')
        ];
    }

    protected function getPlaceholderUrl()
    {
        $destinationSubDir = $this->_getModel()->getDestinationSubdir();

        $placeholderPathFromConfig = $this->scopeConfig->getValue(
            "catalog/placeholder/{$destinationSubDir}_placeholder",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$placeholderPathFromConfig) {
            return $this->_assetRepo->getUrl("Magento_Catalog::images/product/placeholder/{$destinationSubDir}.jpg");
        }

        return $this->mediaBaseUrl . 'catalog/product/placeholder/' . $placeholderPathFromConfig;
    }

    protected function applyScheduledActions()
    {
        return $this;
    }
}
