<?php

namespace MageSuite\LazyResize\Test\Unit\Service;

class TokenGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * @var \MageSuite\LazyResize\Helper\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;


    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->configuration = $this->getMockBuilder(\MageSuite\LazyResize\Helper\Configuration::class)
            ->setMethods(['getTokenSecret'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenGenerator = $objectManager->create(
            \MageSuite\LazyResize\Service\TokenGenerator::class,
            [
                'configuration' => $this->configuration
            ]
        );
    }

    public function testItGeneratesProperToken()
    {
        $this->configuration->method('getTokenSecret')->willReturn('f8f9fb44b4d7c6fe7ecef7091d475170');

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
