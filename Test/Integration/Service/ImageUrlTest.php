<?php

namespace MageSuite\LazyResize\Test\Integration\Service;

class ImageUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler
     */
    protected $imageUrlHandler;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->imageUrlHandler = $objectManager->get(\MageSuite\LazyResize\Service\ImageUrlHandler::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/dev/lazy_resize/token_secret f8f9fb44b4d7c6fe7ecef7091d475170
     */
    public function testItGeneratesProperUrlBasedOnConfiguration()
    {
        $result = $this->imageUrlHandler->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => true,
            'transparency' => false,
            'enable_optimization' => false,
            'optimization_level' => 0,
            'image_file' => '/m/a/magento.jpg'
        ]);

        $this->assertEquals('catalog/product/thumbnail/27b9feaac9e54cd989aa1cc99736483a913ed3efa8f4b4ab70420054/small_image/0/500x0/100/0/m/a/magento.jpg', $result);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/dev/lazy_resize/token_secret f8f9fb44b4d7c6fe7ecef7091d475170
     */
    public function testItGeneratesProperUrlBasedOnConfigurationWithFileSize()
    {
        $result = $this->imageUrlHandler->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'file_size' => 300,
            'height' => 0,
            'aspect_ratio' => true,
            'transparency' => false,
            'enable_optimization' => false,
            'optimization_level' => 0,
            'image_file' => '/m/a/magento.jpg'
        ]);

        $this->assertEquals('catalog/product/thumbnail/fd7d0c64c8957b31fe0cabc9fc049ab052d6c03d86d14ff64b3f6766/small_image/300/500x0/100/0/m/a/magento.jpg', $result);
    }

    public function testItParsesUrlProperlyWithFileSize()
    {
        $result = $this->imageUrlHandler->matchUrl('/media/catalog/product/thumbnail/fd7d0c64c8957b31fe0cabc9fc049ab052d6c03d86d14ff64b3f6766/small_image/400/500x0/100/0/m/a/magento.jpg');

        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'file_size' => '400',
                'aspect_ratio' => true,
                'transparency' => false,
                'enable_optimization' => false,
                'optimization_level' => '0',
                'image_file' => '/m/a/magento.jpg',
                'token' => 'fd7d0c64c8957b31fe0cabc9fc049ab052d6c03d86d14ff64b3f6766',
                'width_and_height' => '500x0',
                'boolean_flags' => '100',
                'first_letter' => 'm',
                'second_letter' => 'a',
                'image_file_path' => 'magento.jpg',
                '_route' => 'resize_with_file_size',
            ],
            $result
        );
    }
}
