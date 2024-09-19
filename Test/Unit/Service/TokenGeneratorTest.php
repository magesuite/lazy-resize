<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\LazyResize\Service\TokenGenerator $tokenGenerator;
    protected ?\MageSuite\LazyResize\Service\Resize\TokenSecretProvider $tokenSecretProviderStub;

    protected function setUp(): void
    {
        $this->tokenSecretProviderStub = $this->getMockBuilder(\MageSuite\LazyResize\Service\Resize\TokenSecretProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator(
            $this->tokenSecretProviderStub
        );
    }

    protected function tearDown(): void
    {
        $reflection = new \ReflectionClass(\MageSuite\LazyResize\Service\TokenGenerator::class);
        $reflection->setStaticPropertyValue('secretToken', null);
    }

    public function testItGeneratesProperToken()
    {
        $this->tokenSecretProviderStub
            ->method('getTokenSecret')
            ->willReturn(\MageSuite\LazyResize\Helper\Configuration::DEFAULT_TOKEN_SECRET);

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

        $this->assertEquals('e11af06b41bcab7aeb2d862eca1dde70c65c76f11a0c679c4803ff79', $result);
    }
}
