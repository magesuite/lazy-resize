<?php

namespace MageSuite\LazyResize\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content;

class ReplaceOriginalUrlsWithResizedVersions
{
    protected \Magento\Catalog\Helper\Image $imageHelper;
    protected \Magento\Framework\Registry $registry;
    protected \Magento\Catalog\Model\Product\Media\Config $mediaConfig;
    protected \Magento\Framework\Filesystem $filesystem;
    protected \Magento\Framework\Image\AdapterFactory $imageAdapterFactory;

    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory
    ) {
        $this->imageHelper = $imageHelper;
        $this->registry = $registry;
        $this->mediaConfig = $mediaConfig;
        $this->filesystem = $filesystem;
        $this->imageAdapterFactory = $imageAdapterFactory;
    }

    public function afterGetImagesJson(\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content $subject, $result)
    {
        $product = $this->registry->registry('current_product');

        $images = json_decode($result, true);

        if (empty($images)) {
            return $result;
        }

        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        foreach ($images as &$image) {
            $imageHelper = $this->imageHelper->init($product, 'product_page_image_large');
            $imageHelper->setImageFile($image['file']);

            $image['url'] = $imageHelper->getUrl();

            $path = $this->mediaConfig->getMediaPath($image['file']);
            $absolutePath = $mediaDirectory->getAbsolutePath($path);

            if (!$mediaDirectory->isExist($path)) {
                continue;
            }

            $imageAdapter = $this->imageAdapterFactory->create();
            $imageAdapter->open($absolutePath);

            $image['image_width'] = $imageAdapter->getOriginalWidth();
            $image['image_height'] = $imageAdapter->getOriginalHeight();
        }

        return json_encode($images);
    }
}
