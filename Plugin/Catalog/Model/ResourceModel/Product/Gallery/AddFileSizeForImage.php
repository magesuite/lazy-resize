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

        $data['file_size'] = $this->getFileSize($filePath);

        return [$data];
    }

    public function beforeSaveDataRow(\Magento\Catalog\Model\ResourceModel\Product\Gallery $subject, $table, array $data, array $fields = [])
    {
        if(!isset($data['value'])) {
            return [$table, $data, $fields];
        }

        $filePath = $data['value'];

        $data['file_size'] = $this->getFileSize($filePath);

        return [$table, $data, $fields];
    }

    public function getFileSize($filePath)
    {
        try {
            $mediaDir = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $mediaPath = $this->mediaConfig->getMediaPath($filePath);
            $fileHandler = $mediaDir->stat($mediaPath);

            return $fileHandler['size'];
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            return 0;
        }
    }
}
