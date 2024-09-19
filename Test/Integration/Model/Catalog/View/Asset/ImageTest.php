<?php

namespace MageSuite\LazyResize\Test\Integration\Model\Catalog\View\Asset;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\Magento\Catalog\Block\Product\ImageBuilder $imageBuilder;
    protected ?\MageSuite\ImageResize\Model\WatermarkConfiguration $watermarkConfiguration;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->imageBuilder = $this->objectManager->get(\Magento\Catalog\Block\Product\ImageBuilder::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->watermarkConfiguration = $this->objectManager->get(\MageSuite\ImageResize\Model\WatermarkConfiguration::class);

        $tokenSecretHelper = $this->objectManager->get(\MageSuite\LazyResize\Test\Integration\TokenSecretHelper::class);
        $tokenSecretHelper->prepareTokenSecretForTests();
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWhenImageIsDefined()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/eeafb757b9dea629e9d4d719418170bffd4c0d041ea06292851fd09d/image/1234/240x300/110/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     * @magentoConfigFixture current_store design/watermark/small_image_image stores/1/thumb.png
     * @magentoConfigFixture current_store design/watermark/small_image_size 200x100
     * @magentoConfigFixture current_store design/watermark/small_image_position top-right
     * @magentoConfigFixture current_store design/watermark/small_image_imageOpacity 50
     */
    public function testItReturnsProperUrlWhenImageHasWatermark()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $this->watermarkConfiguration->setImage('stores/1/thumb.png')
            ->setPosition('top-right')
            ->setOpacity(50)
            ->setWidth(200)
            ->setHeight(100);

        $expectedUrl = sprintf(
            'http://localhost/media/catalog/product/thumbnail/eeafb757b9dea629e9d4d719418170bffd4c0d041ea06292851fd09d/image/1234/240x300/110/0/%s/m/a/magento_image.jpg',
            $this->watermarkConfiguration
        );

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWhenImageAndFileSizeIsDefined()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/eeafb757b9dea629e9d4d719418170bffd4c0d041ea06292851fd09d/image/1234/240x300/110/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store web/url/redirect_to_base 0
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWhenRequestedFromIndexPhp()
    {
        // Below globals are set to emulate scenario where base url contains index.php
        // because request is created based on globals \MageSuite\LazyResize\Service\ImageUrlHandler::66
        $serverVal = $_SERVER; //phpcs:ignore
        $_SERVER['ORIGINAL_URI'] = '/index.php'; //phpcs:ignore
        $_SERVER['REQUEST_URI'] = '/index.php'; //phpcs:ignore

        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/eeafb757b9dea629e9d4d719418170bffd4c0d041ea06292851fd09d/image/1234/240x300/110/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
        $_SERVER = $serverVal; //phpcs:ignore
    }

    /**
     * @param $product
     * @return string
     */
    protected function getImageUrl($product)
    {
        return $this->imageBuilder->create($product, 'category_page_grid', [])->getImageUrl();
    }

    public static function setFileSize()
    {
        require __DIR__ . '/../../../../_files/file_size.php';
    }
}
