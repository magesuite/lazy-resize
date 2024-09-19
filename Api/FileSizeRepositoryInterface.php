<?php

namespace MageSuite\LazyResize\Api;

interface FileSizeRepositoryInterface
{
    /**
     * @param string $filePath
     * @param int $fileSize
     * @return void
     */
    public function addFileSize($filePath, $fileSize): void;

    /**
     * @param string $filePath
     * @return int
     */
    public function getFileSize($filePath): int;

    /**
     * @return \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[]
     * @param \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[] $fileSizes
     */
    public function save(array $fileSizes): array;
}
