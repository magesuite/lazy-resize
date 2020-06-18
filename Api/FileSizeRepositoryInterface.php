<?php

namespace MageSuite\LazyResize\Api;

interface FileSizeRepositoryInterface
{
    /**
     * @param string $filePath
     * @param int $fileSize
     * @return void
     */
    public function addFileSize($filePath, $fileSize);

    /**
     * @param string $filePath
     * @return int
     */
    public function getFileSize($filePath);

    /**
     * @return \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[]
     * @param \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[] $fileSizes
     */
    public function save($fileSizes);
}
