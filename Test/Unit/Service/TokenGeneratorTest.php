<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $tokenSecretProviderStub;

    public function setUp(): void
    {
        $this->tokenSecretProviderStub = $this->getMockBuilder(\MageSuite\LazyResize\Service\Resize\TokenSecretProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator(
            $this->tokenSecretProviderStub
        );
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

        $this->assertEquals('27b9feaac9e54cd989aa1cc99736483a913ed3efa8f4b4ab70420054', $result);
    }
}
