<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class ImageUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\ImageUrl $imageUrl
     */
    protected $imageUrl;

    public function setUp() {
        $this->imageUrl = new \MageSuite\LazyResize\Service\ImageUrl();
    }

    public function testItGeneratesProperUrlBasedOnConfiguration() {
        $result = $this->imageUrl->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => true,
            'transparency' => false,
            'enable_optimization' => false,
            'optimization_level' => 80,
            'image_file' => '/m/a/magento.jpg'
        ]);

        $this->assertEquals('catalog/product/thumbnail/e61c9e15e184913133a02c261fe6435d/small_image/500x0/100/80/m/a/magento.jpg', $result);
    }

    public function testItParsesUrlProperly()
    {
        $result = $this->imageUrl->matchUrl('/media/catalog/product/thumbnail/b5531dfad8d6aa194efeeb269fdb7c58/small_image/500x0/100/80/m/a/magento.jpg');

        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'aspect_ratio' => true,
                'transparency' => false,
                'enable_optimization' => false,
                'optimization_level' => '80',
                'image_file' => '/m/a/magento.jpg',
                'token' => 'b5531dfad8d6aa194efeeb269fdb7c58',
                'width_and_height' => '500x0',
                'boolean_flags' => '100',
                'first_letter' => 'm',
                'second_letter' => 'a',
                'image_file_path' => 'magento.jpg',
                '_route' => 'resize',
            ],
            $result);
    }
}