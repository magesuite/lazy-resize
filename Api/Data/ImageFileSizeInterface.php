<?php

namespace MageSuite\LazyResize\Api\Data;

interface ImageFileSizeInterface
{
    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @return self
     */
    public function setPath($path);

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     * @return self
     */
    public function setSize($size);
}
