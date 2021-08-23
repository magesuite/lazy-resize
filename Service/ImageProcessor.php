<?php

namespace MageSuite\LazyResize\Service;

class ImageProcessor
{
    /**
     * @var \MageSuite\ImageResize\Service\Image\Resize
     */
    protected $imageResize;

    public function __construct(\MageSuite\ImageResize\Service\Image\Resize $imageResize)
    {
        $this->imageResize = $imageResize;
    }

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var \Imagick
     */
    protected $image;

    public function process($configuration)
    {
        $this->configuration = $configuration;

        $this->image = $this->imageResize->resize($configuration);
    }

    public function getMimeType()
    {
        return $this->image->getImageMimeType();
    }

    public function returnToBrowser($fileName = '')
    {
        if (empty($fileName)) {
            return $this->image;
        }

        $contents = @file_get_contents($fileName); //phpcs:ignore

        if ($contents === false) {
            throw new \MageSuite\ImageResize\Exception\OriginalImageNotFound();
        }

        return $contents;
    }

    public function save($requestUri)
    {
        return $this->imageResize->save($requestUri, $this->image);
    }

    public function open($fileName)
    {
        $imageContents = @file_get_contents($fileName); //phpcs:ignore

        if ($imageContents === false) {
            throw new \MageSuite\ImageResize\Exception\OriginalImageNotFound();
        }

        $this->image = new \Imagick();
        $this->image->readImageBlob($imageContents);
    }
}
