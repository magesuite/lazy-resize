<?php

namespace MageSuite\LazyResize\Test\Unit\Repository;

class FileTest extends \PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $assetsDirectoryPath;

    /**
     * @var \MageSuite\LazyResize\Repository\File
     */
    protected $fileRepository;

    public function setUp()
    {
        $this->fileRepository = new \MageSuite\LazyResize\Repository\File();

        $this->assetsDirectoryPath = realpath(__DIR__ . '/../assets');

        $this->fileRepository->setMediaDirectoryPath($this->assetsDirectoryPath);

        $this->cleanUpThumbnailsDirectory();
    }

    public function tearDown()
    {
        $this->cleanUpThumbnailsDirectory();
    }

    public function testItGetsFileContentsProperly()
    {
        $this->assertEquals('existing_file_contents', $this->fileRepository->getOriginalImage('/existing_file'));
    }

    /**
     * @expectedException \MageSuite\LazyResize\Exception\OriginalImageNotFound
     */
    public function testItThrowsExceptionWhenOriginalImageWasNotFound()
    {
        $this->fileRepository->getOriginalImage('/not_existing_file');
    }

    public function testItSavesFileContentsProperly()
    {
        $this->fileRepository->save('catalog/product/thumbnail/500x500/test', 'test_data');

        $targetFilePath = $this->assetsDirectoryPath . '/catalog/product/thumbnail/500x500/test';

        $this->assertTrue(file_exists($targetFilePath));
        $this->assertEquals('test_data', file_get_contents($targetFilePath));
    }

    protected function cleanUpThumbnailsDirectory()
    {
        if (file_exists($this->assetsDirectoryPath . '/catalog/product/thumbnail')) {
            $this->deleteDirectory($this->assetsDirectoryPath . '/catalog/product/thumbnail');
        }
    }

    public function deleteDirectory($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
