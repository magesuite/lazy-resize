<?php

namespace MageSuite\LazyResize\Test\Unit\Model\Catalog\View\Asset;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    const OPTIMIZATION_LEVEL = 60;

    const CORRECT_IMAGE_PATH = 'thumbnail/%s/image/0/400x300/110/0/l/o/logo_correct.png';
    const WRONG_IMAGE_PATH = 'catalog/product/thumbnail/%s/image/400x300/110/0/l/o/logo_wrong.png';

    const MISC_PARAMS = [
        'image_width' => 400,
        'image_height' => 300,
        'keep_frame' => 1,
        'keep_aspect_ratio' => 1,
        'keep_transparency' => 1,
        'background' => 0
    ];

    protected ?\Magento\Framework\App\ObjectManager $objectManager;
    protected ?\MageSuite\LazyResize\Model\Catalog\View\Asset\Image $image;
    protected ?\MageSuite\LazyResize\Service\ImageUrlHandler $urlBuilder;
    protected ?\MageSuite\LazyResize\Model\FileSizeRepository $fileSizeRepository;
    protected ?\MageSuite\LazyResize\Helper\Configuration $configurationStub;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->fileSizeRepository = $this->objectManager->get(\MageSuite\LazyResize\Model\FileSizeRepository::class);

        $this->urlBuilder = $this->getMockBuilder(\MageSuite\LazyResize\Service\ImageUrlHandler::class)
            ->setMethods(['generateUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurationStub = $this->getMockBuilder(\MageSuite\LazyResize\Helper\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItReturnImagePathCorrectlyWithCorrectDirectory()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::CORRECT_IMAGE_PATH);

        $image = $this->objectManager->create(
            \MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_correct.png',
                'miscParams' => self::MISC_PARAMS
            ]
        );

        $assertContains = method_exists($this, 'assertStringContainsString') ? 'assertStringContainsString' : 'assertContains';

        $this->$assertContains('/pub/media/catalog/product/thumbnail', $image->getPath());
        $this->$assertContains('image/0/400x300/110/0/l/o/logo_correct.png', $image->getPath());
    }

    public function testItReturnImagePathCorrectlyWithCorrectDirectoryAndFileSize()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::CORRECT_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_correct.png', 3000);

        $this->generateReturnValueMapForScopeConfig();

        $image = $this->objectManager->create(
            \MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_correct.png',
                'miscParams' => self::MISC_PARAMS,
                'configuration' => $this->configurationStub
            ]
        );

        $path = $image->getPath();

        $assertContains = method_exists($this, 'assertStringContainsString') ? 'assertStringContainsString' : 'assertContains';

        $this->$assertContains('/pub/media/catalog/product/thumbnail', $path);
        $this->$assertContains('image/3000/400x300/110/60/l/o/logo_correct.png', $path);
    }

    public function testItReturnImagePathCorrectlyWithWrongDirectory()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::WRONG_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_wrong.png', 4000);

        $image = $this->objectManager->create(
            \MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_wrong.png',
                'miscParams' => self::MISC_PARAMS
            ]
        );

        $assertContains = method_exists($this, 'assertStringContainsString') ? 'assertStringContainsString' : 'assertContains';

        $this->$assertContains('/pub/media/catalog/product/thumbnail', $image->getPath());
        $this->$assertContains('image/4000/400x300/110/0/l/o/logo_wrong.png', $image->getPath());
    }

    public function testItReturnImagePathCorrectlyWithWrongDirectoryAndFileSize()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::WRONG_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_wrong.png', 4000);
        $this->generateReturnValueMapForScopeConfig();

        $image = $this->objectManager->create(
            \MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_wrong.png',
                'miscParams' => self::MISC_PARAMS,
                'configuration' => $this->configurationStub
            ]
        );

        $path = $image->getPath();

        $assertContains = method_exists($this, 'assertStringContainsString') ? 'assertStringContainsString' : 'assertContains';

        $this->$assertContains('/pub/media/catalog/product/thumbnail', $path);
        $this->$assertContains('image/4000/400x300/110/60/l/o/logo_wrong.png', $path);
    }

    protected function generateReturnValueMapForScopeConfig(): void
    {
        $this->configurationStub
            ->method('isOptimizationEnabled')
            ->willReturn(false);

        $this->configurationStub
            ->method('getOptimizationLevel')
            ->willReturn(self::OPTIMIZATION_LEVEL);

        $this->configurationStub
            ->method('getTokenSecret')
            ->willReturn('123456789abcdefg');
    }
}
