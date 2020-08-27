<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator
     */
    protected $tokenGenerator;

    public function setUp(): void {
        $this->tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator();
    }

    public function testItGeneratesProperToken() {
        $result = $this->tokenGenerator->generate([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => true,
            'transparency' => false,
            'enable_optimization' => false,
            'image_file' => '/m/a/magento.jpg',
            'optimization_level' => 80
        ]);

        $this->assertEquals('e61c9e15e184913133a02c261fe6435d', $result);
    }
}
