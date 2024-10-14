<?php
declare(strict_types=1);

namespace MageSuite\LazyResize\Service;

class WatermarkBuilder
{
    protected \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory;
    protected \Magento\Framework\Filesystem\DirectoryList $directoryList;
    protected \Magento\Framework\Filesystem\Driver\File $fileDriver;
    protected \Magento\Theme\Model\Design\Config\MetadataProvider $metadataProvider;
    protected \Magento\Framework\Stdlib\ArrayManager $arrayManager;
    protected array $fileCache = [];

    public function __construct(
        \MageSuite\ImageResize\Model\WatermarkConfigurationFactory $watermarkConfigurationFactory,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Theme\Model\Design\Config\MetadataProvider $metadataProvider,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager
    ) {
        $this->watermarkConfigurationFactory = $watermarkConfigurationFactory;
        $this->directoryList = $directoryList;
        $this->fileDriver = $fileDriver;
        $this->metadataProvider = $metadataProvider;
        $this->arrayManager = $arrayManager;
    }

    public function create(string $watermarkFile, string $imageType): \MageSuite\ImageResize\Model\WatermarkConfiguration
    {
        $watermark = $this->watermarkConfigurationFactory->create();

        if ($file = $this->getWatermarkFile($watermarkFile, $imageType)) {
            $watermark->setImage($file['path']);
            $watermark->setFilesize($file['size']);
        }

        return $watermark;
    }

    protected function getWatermarkFile(string $file, string $watermarkType): ?array
    {
        $fileCacheKey = sprintf('%s_%s', $watermarkType, $file);

        if (isset($this->fileCache[$fileCacheKey])) {
            return $this->fileCache[$fileCacheKey];
        }

        $uploadDir = $this->arrayManager->get(
            sprintf('watermark_%s_image/upload_dir/value', $watermarkType),
            $this->metadataProvider->get()
        );

        $mediaDirectory = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $fileRelativePath = join(DIRECTORY_SEPARATOR, [$uploadDir, $file]);

        try {
            if (!$this->fileDriver->isFile($mediaDirectory . DIRECTORY_SEPARATOR . $fileRelativePath)) {
                $this->fileCache[$fileCacheKey] = null;
                return null;
            }

            $stat = $this->fileDriver->stat($mediaDirectory . DIRECTORY_SEPARATOR . $fileRelativePath);
            $this->fileCache[$fileCacheKey] = [
                'path' => $fileRelativePath,
                'size' => $stat['size']
            ];
            
            return $this->fileCache[$fileCacheKey];
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            ; // do nothing
        }

        return null;
    }
}
