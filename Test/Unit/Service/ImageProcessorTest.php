<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class ImageProcessorTest extends \PHPUnit\Framework\TestCase
{
    protected string $assetsDirectoryPath = __DIR__ . '/../assets';

    protected ?\MageSuite\LazyResize\Service\ImageProcessor $imageProcessor;
    protected ?\MageSuite\ImageResize\Repository\ImageInterface $imageRepository;
    protected ?\MageSuite\ImageResize\Service\Image\Resize $imageResize;
    protected ?\MageSuite\ImageResize\Service\Image\Watermark $watermark;
    protected ?\MageSuite\ImageResize\Model\Encoder\WatermarkEncoder $watermarkEncoder;
    protected ?\MageSuite\ImageResize\Model\WatermarkConfiguration $watermarkConfiguration;

    protected function setUp(): void
    {
        $this->imageRepository = new \MageSuite\ImageResize\Repository\File();
        $this->watermarkEncoder = new \MageSuite\ImageResize\Model\Encoder\WatermarkEncoder();
        $this->watermarkConfiguration = new \MageSuite\ImageResize\Model\WatermarkConfiguration($this->watermarkEncoder);
        $this->imageRepository->setMediaDirectoryPath($this->assetsDirectoryPath);
        $this->watermark = new \MageSuite\ImageResize\Service\Image\Watermark($this->imageRepository, $this->watermarkConfiguration);

        $this->imageResize = new \MageSuite\ImageResize\Service\Image\Resize(
            $this->imageRepository,
            $this->watermark
        );

        $this->imageProcessor = new \MageSuite\LazyResize\Service\ImageProcessor(
            $this->imageResize
        );

        $this->cleanUpThumbnailsDirectory();
    }

    protected function tearDown(): void
    {
        $this->cleanUpThumbnailsDirectory();
        $this->cleanUpProcessedImagesDirectory();
        $this->cleanStaticSecretToken();
    }

    public function testItResizesImageProperly()
    {
        $configuration['image_file'] = '/l/o/logo.png';
        $configuration['width'] = '200';
        $configuration['height'] = '100';

        $this->imageProcessor->process($configuration);
        $this->imageProcessor->save('catalog/product/thumbnail/200x100/l/o/logo.png');

        [$width, $height] = getimagesize($this->assetsDirectoryPath . '/catalog/product/thumbnail/200x100/l/o/logo.png');

        $this->assertEquals(200, $width);
        $this->assertEquals(100, $height);
    }

    public function testIfImageKeepsTransparency()
    {
        $configuration['image_file'] = '/l/o/logo.png';
        $configuration['width'] = '200';
        $configuration['height'] = '100';

        $this->imageProcessor->process($configuration);
        $this->imageProcessor->save('catalog/product/thumbnail/200x100/l/o/logo.png');

        $image = imagecreatefrompng($this->assetsDirectoryPath . '/catalog/product/thumbnail/200x100/l/o/logo.png');
        $rgba = imagecolorat($image, 0, 0);
        $colors = imagecolorsforindex($image, $rgba);

        $this->assertEquals([
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 127,
        ], $colors);
    }

    protected function cleanUpThumbnailsDirectory()
    {
        if (file_exists($this->assetsDirectoryPath . '/catalog/product/thumbnail')) {
            $this->deleteDirectory($this->assetsDirectoryPath . '/catalog/product/thumbnail');
        }
    }

    public function deleteDirectory($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    protected function cleanUpProcessedImagesDirectory()
    {
        if (file_exists($this->assetsDirectoryPath . '/l')) {
            $this->deleteDirectory($this->assetsDirectoryPath . '/l');
        }
    }

    protected function cleanStaticSecretToken(): void
    {
        $reflection = new \ReflectionClass(\MageSuite\LazyResize\Service\TokenGenerator::class);
        $reflection->setStaticPropertyValue('secretToken', null);
    }
}
