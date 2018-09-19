<?php

namespace MageSuite\LazyResize\Repository;

class File implements Image
{
    protected $mediaDirectoryPath;

    public function __construct()
    {
        $this->mediaDirectoryPath = BP . '/pub/media';
    }

    public function setMediaDirectoryPath($path) {
        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf("Folder %s does not exist", $path));
        }

        $this->mediaDirectoryPath = $path;
    }

    /**
     * Gets original image content for specified path
     * @param $path
     * @return mixed
     */
    public function getOriginalImage($path)
    {
        $contents = @file_get_contents($this->mediaDirectoryPath . '/catalog/product' . $path);
        
        if($contents === false) {
            throw new \MageSuite\LazyResize\Exception\OriginalImageNotFound();
        }

        return $contents;
    }

    /**
     * Saves resized image content to specified path
     * @param $path
     * @param $data
     * @return mixed
     */
    public function save($path, $data)
    {
        $path = $this->mediaDirectoryPath . '/' . $path;

        $targetDirectory = $this->normalizePath(dirname($path));

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        file_put_contents($path, (string)$data);

        return $path;
    }

    private function normalizePath($path)
    {
        return array_reduce(explode('/', $path), function ($a, $b) {
            if ($a === 0) {
                $a = "/";
            }
            if ($b === "" || $b === "."){
                return $a;
            }
            if ($b === "..") {
                return dirname($a);
            }
            return preg_replace("/\/+/", "/", "$a/$b");
        }, 0);
    }
}