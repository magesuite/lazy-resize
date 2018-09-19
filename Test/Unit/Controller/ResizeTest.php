<?php

namespace MageSuite\LazyResize\Test\Unit\Controller;

class ResizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Controller\Resize
     */
    protected $controller;

    /**
     * @var \MageSuite\LazyResize\Service\UrlParser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlParserDouble;

    /**
     * @var \MageSuite\LazyResize\Service\ImageProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageProcessorDouble;

    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokenGeneratorDouble;

    /**
     * @var \MageSuite\Frontend\Service\Image\Optimizer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageOptimizerDouble;

    public function setUp()
    {
        $this->tokenGeneratorDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\TokenGenerator::class)->getMock();
        $this->urlParserDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\UrlParser::class)->getMock();
        $this->imageProcessorDouble = $this->getMockBuilder(\MageSuite\LazyResize\Service\ImageProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->imageOptimizerDouble = $this->getMockBuilder(\MageSuite\Frontend\Service\Image\Optimizer::class)->getMock();

        $this->controller = new \MageSuite\LazyResize\Controller\Resize(
            $this->tokenGeneratorDouble,
            $this->urlParserDouble,
            $this->imageProcessorDouble,
            $this->imageOptimizerDouble
        );
    }

    public function testItReturns404WhenTokenDoesNotMatch()
    {
        $this->urlParserDouble->method('parseUrl')->willReturn(['token' => 'returned_token']);
        $this->tokenGeneratorDouble->method('generate')->willReturn('not_matching_token');

        $result = $this->controller->execute('catalog/product/thumbnail/l/o/logo.png');

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testItRedirectsToPlaceholderWhenOriginalImageDoesNotExist()
    {
        $this->urlParserDouble->method('parseUrl')->willReturn(['token' => 'matching_token', 'type' => 'small_image']);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');
        $this->imageProcessorDouble->method('process')->willThrowException(
            new \MageSuite\LazyResize\Exception\OriginalImageNotFound()
        );

        $result = $this->controller->execute('catalog/product/thumbnail/l/o/logo.png');

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $result);
        $this->assertEquals('/media/placeholders/small_image.png', $result->getTargetUrl());
    }

    public function testItProcessesAndSavesImage()
    {
        $returnedConfiguration = ['token' => 'matching_token'];
        $requestUri = 'catalog/product/thumbnail/l/o/logo.png';

        $this->urlParserDouble->method('parseUrl')->willReturn($returnedConfiguration);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');

        $this->imageProcessorDouble->expects($this->once())->method('process')->with($returnedConfiguration);
        $this->imageProcessorDouble->expects($this->once())->method('save')->with($requestUri);

        $this->controller->execute($requestUri);
    }

    public function testItReturnsImageToBrowser()
    {
        $returnedConfiguration = ['token' => 'matching_token'];
        $requestUri = 'catalog/product/thumbnail/l/o/logo.png';

        $this->urlParserDouble->method('parseUrl')->willReturn($returnedConfiguration);
        $this->tokenGeneratorDouble->method('generate')->willReturn('matching_token');

        $this->imageProcessorDouble->method('returnToBrowser')->willReturn('returned_content');
        $this->imageProcessorDouble->method('getMimeType')->willReturn('image/jpeg');

        $result = $this->controller->execute($requestUri);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('returned_content', $result->getContent());
        $this->assertEquals('image/jpeg', $result->headers->get('Content-Type'));
    }
}