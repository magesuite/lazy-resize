<?php
namespace MageSuite\LazyResize\Test\Unit\Model\Catalog\View\Asset;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    const CORRECT_IMAGE_PATH = 'thumbnail/200x100/l/o/logo_correct.png';

    const WRONG_IMAGE_PATH = 'catalog/product/thumbnail/200x100/l/o/logo_wrong.png';

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
     * @var \MageSuite\LazyResize\Service\UrlBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    public function setUp() {
        /** @var \Magento\Framework\App\ObjectManager objectManager */
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->urlBuilder = $this->getMockBuilder(\MageSuite\LazyResize\Service\UrlBuilder::class)
            ->setMethods(['buildUrl'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testItReturnImagePathCorrectlyWithCorrectDirectory()
    {
        $this->urlBuilder->method('buildUrl')
            ->willReturn(self::CORRECT_IMAGE_PATH);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => self::CORRECT_IMAGE_PATH,
                'miscParams' => self::MISC_PARAMS
            ]
        )->setUrlBuilder($this->urlBuilder);

        $this->assertContains('/pub/media/catalog/product/' . self::CORRECT_IMAGE_PATH, $image->getPath());

    }

    public function testItReturnImagePathCorrectlyWithWrongDirectory()
    {
        $this->urlBuilder->method('buildUrl')
            ->willReturn(self::WRONG_IMAGE_PATH);

        $image = $this->objectManager->create(\MageSuite\LazyResize\Model\Catalog\View\Asset\Image::class,
            [
                'filePath' => self::WRONG_IMAGE_PATH,
                'miscParams' => self::MISC_PARAMS
            ]
        )->setUrlBuilder($this->urlBuilder);

        $this->assertContains('/pub/media/' . self::WRONG_IMAGE_PATH, $image->getPath());

    }
}
