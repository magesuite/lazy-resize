<?php

namespace MageSuite\LazyResize\Test\Integration\Service;

class ImageUrlHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler
     */
    protected $imageUrlHandler;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->imageUrlHandler = $objectManager->get(\MageSuite\LazyResize\Service\ImageUrlHandler::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testItGeneratesProperUrlWithBooleanParameters()
    {
        $url = $this->imageUrlHandler->generateUrl([
            'type' => 'small_image',
            'width' => 500,
            'height' => 0,
            'aspect_ratio' => '0',
            'transparency' => '0',
            'enable_optimization' => '0',
            'optimization_level' => 0,
            'image_file' => '/m/a/magento.jpg'
        ]);
        $expectedUrl = 'catalog/product/thumbnail/a55375d8a95b22db6261ccdca5e20107/small_image/500x0/000/0/m/a/magento.jpg';
        $this->assertEquals($expectedUrl, $url);

        $matchUrl = $this->imageUrlHandler->matchUrl('/media/' . $expectedUrl);
        $this->assertEquals(
            [
                'type' => 'small_image',
                'width' => 500,
                'height' => 0,
                'aspect_ratio' => '0',
                'transparency' => '0',
                'enable_optimization' => '0',
                'optimization_level' => '0',
                'image_file' => '/m/a/magento.jpg',
                'token' => 'a55375d8a95b22db6261ccdca5e20107',
                'width_and_height' => '500x0',
                'boolean_flags' => '000',
                'first_letter' => 'm',
                'second_letter' => 'a',
                'image_file_path' => 'magento.jpg',
                '_route' => 'resize',
            ],
            $matchUrl
        );
    }
}
