<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class UrlParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\UrlParser
     */
    protected $urlParser;

    public function setUp()
    {
        $this->urlParser = new \MageSuite\LazyResize\Service\UrlParser();
    }

    public function testItParsesUrlProperly()
    {
        $result = $this->urlParser->parseUrl('catalog/product/thumbnail/b5531dfad8d6aa194efeeb269fdb7c58/small_image/500x0/100/80/m/a/magento.jpg');

        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'aspect_ratio' => true,
                'transparency' => false,
                'enable_optimization' => false,
                'optimization_level' => 80,
                'image_file' => '/m/a/magento.jpg',
                'token' => 'b5531dfad8d6aa194efeeb269fdb7c58'
            ],
            $result);
    }
}