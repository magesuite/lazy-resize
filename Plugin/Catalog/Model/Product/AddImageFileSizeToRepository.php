<?php
declare(strict_types=1);

namespace MageSuite\LazyResize\Plugin\Catalog\Model\Product;

class AddImageFileSizeToRepository
{
    protected \MageSuite\LazyResize\Api\FileSizeRepositoryInterface $fileSizeRepository;

    public function __construct(\MageSuite\LazyResize\Api\FileSizeRepositoryInterface $fileSizeRepository)
    {
        $this->fileSizeRepository = $fileSizeRepository;
    }

    public function afterGetMediaGalleryImages(
        \Magento\Catalog\Model\Product $subject,
        $result
    ) {
        if (!$result instanceof \Magento\Framework\Data\Collection) {
            return $result;
        }

        foreach ($result as $image) {
            $this->fileSizeRepository->addFileSize(
                $image->getData('file'),
                $image->getData('file_size')
            );
        }

        return $result;
    }
}
