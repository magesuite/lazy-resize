<?php

namespace MageSuite\LazyResize\Plugin\Catalog\Model\ResourceModel\Product\Gallery;

class AddFileSizeForImage
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    public function __construct(
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $fileSystem
    )
    {
        $this->fileSystem = $fileSystem;
        $this->mediaConfig = $mediaConfig;
    }

    public function beforeInsertGallery(\Magento\Catalog\Model\ResourceModel\Product\Gallery $subject, $data)
    {
        if(!isset($data['value'])) {
            return [$data];
        }

        $filePath = $data['value'];

        try {
            $mediaDir = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $mediaPath = $this->mediaConfig->getMediaPath($filePath);
            $fileHandler = $mediaDir->stat($mediaPath);

            $data['file_size'] = $fileHandler['size'];
        }
        catch(\Magento\Framework\Exception\FileSystemException $e) {

        }

        return [$data];
    }
}
