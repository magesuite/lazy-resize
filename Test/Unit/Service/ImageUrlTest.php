<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class ImageUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\ImageUrl $imageUrlHandler
     */
    protected $imageUrlHandler;

    public function setUp(): void
    {
        $this->imageUrlHandler = new \MageSuite\LazyResize\Service\ImageUrlHandler();
    }

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

        $this->assertEquals('catalog/product/thumbnail/75d96cecbf89bbe940985bb795a4db38cff5810b6b4a6f50f1ac169c/small_image/0/500x0/100/0/m/a/magento.jpg', $result);
    }

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

        $this->assertEquals('catalog/product/thumbnail/857c018fbaea381b3287ba235746e43b19c71b81ab048fd4fe27da61/small_image/300/500x0/100/0/m/a/magento.jpg', $result);
    }

    public function testItParsesUrlProperlyWithFileSize()
    {
        $result = $this->imageUrlHandler->matchUrl('/media/catalog/product/thumbnail/127bad9ac8625137b06402854820b84681f9315e3ec01a08d2622750/small_image/400/500x0/100/0/m/a/magento.jpg');

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
                'token' => '127bad9ac8625137b06402854820b84681f9315e3ec01a08d2622750',
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
