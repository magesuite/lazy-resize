<?php

namespace MageSuite\LazyResize\Plugin\Catalog\Model\Product\Gallery\ReadHandler;

class GatherFileSizesFromMediaEntries
{
    /**
     * @var \MageSuite\LazyResize\Model\FileSizeRepository
     */
    protected $fileSizeRepository;

    public function __construct(\MageSuite\LazyResize\Model\FileSizeRepository $fileSizeRepository)
    {
        $this->fileSizeRepository = $fileSizeRepository;
    }

    public function afterAddMediaDataToProduct(\Magento\Catalog\Model\Product\Gallery\ReadHandler $subject, $result, \Magento\Catalog\Model\Product $product, array $mediaEntries)
    {
        if (empty($mediaEntries)) {
            return;
        }

        foreach ($mediaEntries as $mediaEntry) {
            $this->fileSizeRepository->addFileSize($mediaEntry['file'], $mediaEntry['file_size']);
        }
    }
}
