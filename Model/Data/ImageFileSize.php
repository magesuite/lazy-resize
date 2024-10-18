<?php

namespace MageSuite\LazyResize\Model\Data;

class ImageFileSize implements \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface
{
    /**
     * @var string
     */
    protected ?string $path;

    /**
     * @var int
     */
    protected ?int $size;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface
    {
        $this->path = $path;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface
    {
        $this->size = $size;
        return $this;
    }
}
