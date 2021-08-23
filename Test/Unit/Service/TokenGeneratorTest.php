<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator
     */
    protected $tokenGenerator;

    public function setUp(): void
    {
        $this->tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator();
    }

    public function testItGeneratesProperToken()
    {
        $result = $this->tokenGenerator->generate([
            'type' => 'small_image',
            'file_size' => 0,
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => true,
            'transparency' => false,
            'enable_optimization' => false,
            'image_file' => '/m/a/magento.jpg',
            'optimization_level' => 0
        ]);

        $this->assertEquals('75d96cecbf89bbe940985bb795a4db38cff5810b6b4a6f50f1ac169c', $result);
    }
}
