<?php

namespace MageSuite\LazyResize\Service;

class ImageProcessor
{
    const BACKGROUD_WHITE = 'white';
    const BACKGROUD_TRANSPARENT = 'transparent';

    /**
     * @var \MageSuite\LazyResize\Repository\Image
     */
    private $imageRepository;

    /**
     * @var \MageSuite\Frontend\Service\Image\Optimizer
     */
    private $imageOptimizer;

    public function __construct(
        \MageSuite\LazyResize\Repository\Image $imageRepository,
        \MageSuite\Frontend\Service\Image\Optimizer $imageOptimizer
    )
    {
        $this->imageRepository = $imageRepository;
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

        $imageContents = $this->imageRepository->getOriginalImage($configuration['image_file']);

        $this->image = new \Imagick();

        $this->image->readImageBlob($imageContents);

        $backgroundColor = ImageProcessor::BACKGROUD_WHITE;

        if ($this->getMimeType() === 'image/png') {
            $backgroundColor = ImageProcessor::BACKGROUD_TRANSPARENT;
        }

        $background = new \Imagick();
        $background->newImage($configuration['width'], $configuration['height'], $backgroundColor);

        $this->image->scaleImage($configuration['width'], $configuration['height'], true);

        $w = $this->image->getImageWidth();
        $h = $this->image->getImageHeight();

        $background->compositeImage(
            $this->image,
            \Imagick::COMPOSITE_DEFAULT,
            -($w - $configuration['width']) / 2,
            -($h - $configuration['height']) / 2
        );

        $background->setFilename($configuration['image_file']);
        $this->image = $background;
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

        if($contents === false) {
            throw new \MageSuite\LazyResize\Exception\OriginalImageNotFound();
        }

        return $contents;
    }

    public function save($requestUri)
    {
        return $this->imageRepository->save($requestUri, (string)$this->image);
    }

    public function open($fileName)
    {
        $imageContents = @file_get_contents($fileName);
        if($imageContents === false) {
            throw new \MageSuite\LazyResize\Exception\OriginalImageNotFound();
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