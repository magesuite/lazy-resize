<?php

namespace MageSuite\LazyResize\Service;

class ImageProcessor
{
    /**
     * @var \MageSuite\ImageResize\Service\Image\Resize
     */
    protected $imageResize;

    /**
     * @var \MageSuite\ImageOptimization\Service\Image\Optimizer
     */
    protected $imageOptimizer;

    public function __construct(
        \MageSuite\ImageResize\Service\Image\Resize $imageResize,
        \MageSuite\ImageOptimization\Service\Image\Optimizer $imageOptimizer
    ) {
        $this->imageResize = $imageResize;
        $this->imageOptimizer = $imageOptimizer;
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

        $contents = @file_get_contents($fileName);

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
        $imageContents = @file_get_contents($fileName);

        if ($imageContents === false) {
            throw new \MageSuite\ImageResize\Exception\OriginalImageNotFound();
        }

        $this->image = new \Imagick();
        $this->image->readImageBlob($imageContents);
    }

    public function optimize($configuration)
    {
        $tmpFile = sys_get_temp_dir() . '/M2C_' . md5($configuration['token'] . $configuration['image_file']);
        file_put_contents($tmpFile, $this->returnToBrowser());
        $this->imageOptimizer->optimize($tmpFile);
        $this->open($tmpFile);
        unlink($tmpFile);
    }
}
