<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class ImageProcessorTest extends \PHPUnit\Framework\TestCase
{
    protected $assetsDirectoryPath = __DIR__ . '/../assets';
    /**
     * @var \MageSuite\LazyResize\Service\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var \MageSuite\ImageResize\Repository\ImageInterface
     */
    protected $imageRepository;

    /**
     * @var \MageSuite\ImageResize\Service\Image\Resize
     */
    protected $imageResize;

    public function setUp(): void
    {
        $this->imageRepository = new \MageSuite\ImageResize\Repository\File();
        $this->imageRepository->setMediaDirectoryPath($this->assetsDirectoryPath);

        $this->imageResize = new \MageSuite\ImageResize\Service\Image\Resize(
            $this->imageRepository
        );

        $this->imageProcessor = new \MageSuite\LazyResize\Service\ImageProcessor(
            $this->imageResize,
            new \MageSuite\ImageOptimization\Service\Image\CommandLine\Optimizer(
                new \ImageOptimizer\OptimizerFactory([
                    'jpegoptim_options' => ['--max=80'],
                    'execute_only_first_jpeg_optimizer' => false,
                    'execute_only_first_png_optimizer' => false
                ])
            )
        );

        $this->cleanUpThumbnailsDirectory();
    }

    public function tearDown(): void
    {
        $this->cleanUpThumbnailsDirectory();
        $this->cleanUpProcessedImagesDirectory();
    }

    public function testItResizesImageProperly()
    {
        $configuration['image_file'] = '/l/o/logo.png';
        $configuration['width'] = '200';
        $configuration['height'] = '100';

        $this->imageProcessor->process($configuration);
        $this->imageProcessor->save('catalog/product/thumbnail/200x100/l/o/logo.png');

        list($width, $height) = getimagesize($this->assetsDirectoryPath.'/catalog/product/thumbnail/200x100/l/o/logo.png');

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

        $image = imagecreatefrompng($this->assetsDirectoryPath.'/catalog/product/thumbnail/200x100/l/o/logo.png');
        $rgba = imagecolorat($image, 0, 0);
        $colors = imagecolorsforindex($image, $rgba);

        $this->assertEquals([
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 127
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

    public function testIfOptimizePngImageWithTransparencyProperly()
    {
        $fileName = '/l/o/logo.png';
        $configuration = [
            'image_file' => $fileName,
            'token' => '4b480ef5debc72f2bd51472055f12d23',
            'width' => '200',
            'height' => '100'
        ];
        $this->imageProcessor->process($configuration);
        $processedFileName = $this->imageProcessor->save($fileName);
        $originalFileSize = filesize($processedFileName);
        $this->imageProcessor->optimize($configuration);
        $this->imageProcessor->save($fileName);
        $optimizedFileSize = filesize($processedFileName);

        /**
         * This file after optmimization is bigger than original, because it keeps transparency
         */
        $this->assertGreaterThan($originalFileSize, $optimizedFileSize);
    }

    public function testIfOptimizePngImageWithoutTransparencyProperly()
    {
        $fileName = '/l/o/logo_no_transparency.png';
        $configuration = [
            'image_file' => $fileName,
            'token' => '4b480ef5debc72f2bd51472055f12d23',
            'width' => '200',
            'height' => '100'
        ];
        $this->imageProcessor->process($configuration);
        $processedFileName = $this->imageProcessor->save($fileName);
        $originalFileSize = filesize($processedFileName);
        $this->imageProcessor->optimize($configuration);
        $this->imageProcessor->save($fileName);
        $optimizedFileSize = filesize($processedFileName);

        $this->assertLessThan($originalFileSize, $optimizedFileSize);
    }

    public function testIfOptimizeJpegImageProperly()
    {
        $fileName = '/l/o/logo.jpg';
        $configuration = [
            'image_file' => $fileName,
            'token' => '4b480ef5debc72f2bd51472055f12d23',
            'width' => '200',
            'height' => '100'
        ];
        $this->imageProcessor->process($configuration);
        $processedFileName = $this->imageProcessor->save($fileName);
        $originalFileSize = filesize($processedFileName);
        $this->imageProcessor->optimize($configuration);
        $this->imageProcessor->save($fileName);
        $optimizedFileSize = filesize($processedFileName);

        $this->assertLessThan($originalFileSize, $optimizedFileSize);
    }

    protected function cleanUpProcessedImagesDirectory()
    {
        if (file_exists($this->assetsDirectoryPath . '/l')) {
            $this->deleteDirectory($this->assetsDirectoryPath . '/l');
        }
    }
}
