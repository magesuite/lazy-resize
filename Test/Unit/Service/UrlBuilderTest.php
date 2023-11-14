<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class UrlBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\UrlBuilder
     */
    protected $urlBuilder;

    public function setUp() {
        $this->urlBuilder = new \MageSuite\LazyResize\Service\UrlBuilder(new \MageSuite\LazyResize\Service\TokenGenerator());
    }

    public function testItGeneratesProperUrlBasedOnConfiguration() {
        $result = $this->urlBuilder->buildUrl([
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
}
