<?php

namespace MageSuite\LazyResize\Test\Unit\Controller;

class ResizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Controller\Resize
     */
    protected $controller;

    /**
     * @var \MageSuite\LazyResize\Service\ImageProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageProcessorDouble;

    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenGeneratorDouble;

    /**
     * @var \MageSuite\ImageOptimization\Service\Image\Optimizer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageOptimizerDouble;

    /**
     * @var \MageSuite\LazyResize\Service\ImageUrl $imageUrlHandlerDouble
     */
    protected $imageUrlHandlerDouble;

    public function setUp(): void
    {
        $this->tokenGeneratorDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\TokenGenerator::class)->getMock();
        $this->imageProcessorDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\ImageProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->imageUrlHandlerDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\ImageUrlHandler::class)->getMock();

        $this->imageOptimizerDouble = $this->getMockBuilder(\MageSuite\ImageOptimization\Service\Image\Optimizer::class)->getMock();

        $this->controller = new \MageSuite\LazyResize\Controller\Resize(
            $this->tokenGeneratorDouble,
            $this->imageUrlHandlerDouble,
            $this->imageProcessorDouble
        );
    }

    public function testItReturns404WhenTokenDoesNotMatch()
    {
        $this->imageUrlHandlerDouble->method('parseUrl')->willReturn(['token' => 'returned_token']);
        $this->tokenGeneratorDouble->method('generate')->willReturn('not_matching_token');

        $result = $this->controller->execute('catalog/product/thumbnail/l/o/logo.png');

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testItRedirectsToPlaceholderWhenOriginalImageDoesNotExist()
    {
        $this->imageUrlHandlerDouble->method('parseUrl')->willReturn(['token' => 'matching_token', 'type' => 'small_image']);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');
        $this->imageProcessorDouble->method('process')->willThrowException(
            new \MageSuite\ImageResize\Exception\OriginalImageNotFound()
        );

        $result = $this->controller->execute('catalog/product/thumbnail/l/o/logo.png');

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $result);
        $this->assertEquals('/media/placeholders/small_image.png', $result->getTargetUrl());
    }

    public function testItProcessesAndSavesImage()
    {
        $returnedConfiguration = ['token' => 'matching_token'];
        $requestUri = 'catalog/product/thumbnail/l/o/logo.png';

        $this->imageUrlHandlerDouble->method('parseUrl')->willReturn($returnedConfiguration);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');

        $this->imageProcessorDouble->expects($this->once())->method('process')->with($returnedConfiguration);
        $this->imageProcessorDouble->expects($this->once())->method('save')->with($requestUri);

        $this->controller->execute($requestUri);
    }

    public function testItReturnsImageToBrowser()
    {
        $returnedConfiguration = ['token' => 'matching_token'];
        $requestUri = 'catalog/product/thumbnail/l/o/logo.png';

        $this->imageUrlHandlerDouble->method('parseUrl')->willReturn($returnedConfiguration);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');

        $this->imageProcessorDouble->method('returnToBrowser')->willReturn('returned_content');
        $this->imageProcessorDouble->method('getMimeType')->willReturn('image/jpeg');

        $result = $this->controller->execute($requestUri);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('returned_content', $result->getContent());
        $this->assertEquals('image/jpeg', $result->headers->get('Content-Type'));
    }
}
