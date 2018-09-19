<?php

namespace MageSuite\LazyResize\Repository;

interface Image
{
    /**
     * Gets original image content for specified path
     * @param $path
     * @return mixed
     */
    public function getOriginalImage($path);

    /**
     * Saves resized image content to specified path
     * @param $path
     * @param $data
     * @return mixed
     */
    public function save($path, $data);
}