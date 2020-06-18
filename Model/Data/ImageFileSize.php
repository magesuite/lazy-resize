<?php

namespace MageSuite\LazyResize\Model\Data;

class ImageFileSize implements \MageSuite\LazyResize\Api\Data\ImageFileSizeInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $size;

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
}
