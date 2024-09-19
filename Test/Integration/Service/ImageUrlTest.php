<?php

namespace MageSuite\LazyResize\Test\Integration\Service;

class ImageUrlTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler;
    protected ?\MageSuite\ImageResize\Model\WatermarkConfiguration $watermarkConfiguration;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->imageUrlHandler = $objectManager->get(\MageSuite\LazyResize\Service\ImageUrlHandler::class);
        $this->watermarkConfiguration = $objectManager->get(\MageSuite\ImageResize\Model\WatermarkConfiguration::class);

        $tokenSecretHelper = $objectManager->get(\MageSuite\LazyResize\Test\Integration\TokenSecretHelper::class);
        $tokenSecretHelper->prepareTokenSecretForTests();
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
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

        $this->assertEquals('catalog/product/thumbnail/e11af06b41bcab7aeb2d862eca1dde70c65c76f11a0c679c4803ff79/small_image/0/500x0/100/0/m/a/magento.jpg', $result);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
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

        $this->assertEquals('catalog/product/thumbnail/10a9ab7f13e8fd6d635b170074fe70d7b585e617e19a38dbbdbd77cb/small_image/300/500x0/100/0/m/a/magento.jpg', $result);
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

    public function testItGeneratesProperUrlWithBooleanParameters()
    {
        $url = $this->imageUrlHandler->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => '0',
            'transparency' => '0',
            'enable_optimization' => '0',
            'optimization_level' => 0,
            'image_file' => '/m/a/magento.jpg'
        ]);
        $expectedUrl = 'catalog/product/thumbnail/0456cc204bac5231483ab88761611efd9d4e62d6801c5b1d9bd77736/small_image/0/500x0/000/0/m/a/magento.jpg';
        $this->assertEquals($expectedUrl, $url);

        $matchUrl = $this->imageUrlHandler->matchUrl('/media/' . $expectedUrl);
        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'file_size' => '0',
                'aspect_ratio' => '0',
                'transparency' => '0',
                'enable_optimization' => '0',
                'optimization_level' => 0,
                'image_file' => '/m/a/magento.jpg',
                'token' => '0456cc204bac5231483ab88761611efd9d4e62d6801c5b1d9bd77736',
                'width_and_height' => '500x0',
                'boolean_flags' => '000',
                'first_letter' => 'm',
                'second_letter' => 'a',
                'image_file_path' => 'magento.jpg',
                '_route' => 'resize_with_file_size',
            ],
            $matchUrl
        );
    }

    public function testItGeneratesProperUrlWithWatermarkParameters()
    {
        $watermarkConfiguration = $this->watermarkConfiguration
            ->setImage('stores/1/thumb.png')
            ->setPosition('top-right')
            ->setOpacity(50)
            ->setWidth(200)
            ->setHeight(100);

        $url = $this->imageUrlHandler->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => '0',
            'transparency' => '0',
            'enable_optimization' => '0',
            'optimization_level' => 0,
            'image_file' => '/m/a/magento.jpg',
            'watermark' => $watermarkConfiguration

        ]);
        $expectedUrl = \sprintf(
            'catalog/product/thumbnail/0456cc204bac5231483ab88761611efd9d4e62d6801c5b1d9bd77736/small_image/0/500x0/000/0/%s/m/a/magento.jpg',
            $watermarkConfiguration
        );

        $this->assertEquals($expectedUrl, $url);

        $matchUrl = $this->imageUrlHandler->matchUrl('/media/' . $expectedUrl);
        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'file_size' => '0',
                'aspect_ratio' => '0',
                'transparency' => '0',
                'enable_optimization' => '0',
                'optimization_level' => 0,
                'image_file' => '/m/a/magento.jpg',
                'token' => '0456cc204bac5231483ab88761611efd9d4e62d6801c5b1d9bd77736',
                'width_and_height' => '500x0',
                'boolean_flags' => '000',
                'first_letter' => 'm',
                'second_letter' => 'a',
                'image_file_path' => 'magento.jpg',
                '_route' => 'resize_with_file_size_watermark',
                'watermark' => $watermarkConfiguration->encrypt()
            ],
            $matchUrl
        );
    }
}
