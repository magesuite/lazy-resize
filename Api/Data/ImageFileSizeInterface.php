<?php

namespace MageSuite\LazyResize\Api\Data;

interface ImageFileSizeInterface
{
    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string $path
     * @return self
     */
    public function setPath(string $path): ImageFileSizeInterface;

    /**
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * @param int $size
     * @return self
     */
    public function setSize(int $size): ImageFileSizeInterface;
}
