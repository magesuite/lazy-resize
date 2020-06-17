<?php

namespace MageSuite\LazyResize\Test\Unit\Model\Catalog\View\Asset;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    const CORRECT_IMAGE_PATH = 'thumbnail/%s/image/400x300/110/80/l/o/logo_correct.png';

    const WRONG_IMAGE_PATH = 'catalog/product/thumbnail/%s/image/400x300/110/80/l/o/logo_wrong.png';

    const MISC_PARAMS = [
        'image_width' => 400,
        'image_height' => 300,
        'keep_frame' => 1,
        'keep_aspect_ratio' => 1,
        'keep_transparency' => 1,
        'background' => 0
    ];

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MageSuite\LazyResize\Model\Catalog\View\Asset\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $image;

    /**
     * @var \MageSuite\LazyResize\Service\ImageUrlHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \MageSuite\LazyResize\Model\FileSizeRepository
     */
    protected $fileSizeRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $scopeConfigStub;

    public function setUp()
    {
        /** @var \Magento\Framework\App\ObjectManager objectManager */
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->fileSizeRepository = $this->objectManager->get(\MageSuite\LazyResize\Model\FileSizeRepository::class);

        $this->urlBuilder = $this->getMockBuilder(\MageSuite\LazyResize\Service\ImageUrlHandler::class)
            ->setMethods(['generateUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfigStub = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItReturnImagePathCorrectlyWithCorrectDirectory()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::CORRECT_IMAGE_PATH);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_correct.png',
                'miscParams' => self::MISC_PARAMS
            ]
        );

        $this->assertContains('/pub/media/catalog/product/thumbnail', $image->getPath());
        $this->assertContains('image/400x300/110/80/l/o/logo_correct.png', $image->getPath());
    }

    public function testItReturnImagePathCorrectlyWithCorrectDirectoryAndFileSize()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::CORRECT_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_correct.png', 3000);

        $this->generateReturnValueMapForScopeConfig(1);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_correct.png',
                'miscParams' => self::MISC_PARAMS,
                'scopeConfig' => $this->scopeConfigStub
            ]
        );

        $path = $image->getPath();

        $this->assertContains('/pub/media/catalog/product/thumbnail', $path);
        $this->assertContains('image/3000/400x300/110/80/l/o/logo_correct.png', $path);
    }

    public function testItReturnImagePathCorrectlyWithWrongDirectory()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::WRONG_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_wrong.png', 4000);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_wrong.png',
                'miscParams' => self::MISC_PARAMS
            ]
        );

        $this->assertContains('/pub/media/catalog/product/thumbnail', $image->getPath());
        $this->assertContains('image/400x300/110/80/l/o/logo_wrong.png', $image->getPath());

    }

    public function testItReturnImagePathCorrectlyWithWrongDirectoryAndFileSize()
    {
        $this->urlBuilder->method('generateUrl')
            ->willReturn(self::WRONG_IMAGE_PATH);

        $this->fileSizeRepository->addFileSize('l/o/logo_wrong.png', 4000);
        $this->generateReturnValueMapForScopeConfig(1);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => 'l/o/logo_wrong.png',
                'miscParams' => self::MISC_PARAMS,
                'scopeConfig' => $this->scopeConfigStub
            ]
        );

        $path = $image->getPath();

        $this->assertContains('/pub/media/catalog/product/thumbnail', $path);
        $this->assertContains('image/4000/400x300/110/80/l/o/logo_wrong.png', $path);
    }

    protected function generateReturnValueMapForScopeConfig($includeImageFileSizeInUrl)
    {
        return $this->scopeConfigStub->method('getValue')
            ->will($this->onConsecutiveCalls($includeImageFileSizeInUrl, 0, 80));
    }
}
