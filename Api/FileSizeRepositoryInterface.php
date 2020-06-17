<?php

namespace MageSuite\LazyResize\Api;

interface FileSizeRepositoryInterface
{
    /**
     * @return \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[]
     * @param \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface[] $fileSizes
     */
    public function save($fileSizes);
}
