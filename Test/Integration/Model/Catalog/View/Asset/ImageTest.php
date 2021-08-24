<?php

namespace MageSuite\LazyResize\Test\Integration\Model\Catalog\View\Asset;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->imageBuilder = $this->objectManager->get(\Magento\Catalog\Block\Product\ImageBuilder::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoConfigFixture default/dev/lazy_resize/token_secret f8f9fb44b4d7c6fe7ecef7091d475170
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
     * @magentoConfigFixture default/dev/lazy_resize/token_secret f8f9fb44b4d7c6fe7ecef7091d475170
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
     * @magentoConfigFixture default/dev/lazy_resize/token_secret f8f9fb44b4d7c6fe7ecef7091d475170
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWhenRequestedFromIndexPhp()
    {
        // Below globals are set to emulate scenario where base url contains index.php
        // because request is created based on globals \MageSuite\LazyResize\Service\ImageUrlHandler::66
        $_SERVER['ORIGINAL_URI'] = '/index.php'; //phpcs:ignore
        $_SERVER['REQUEST_URI'] = '/index.php'; //phpcs:ignore

        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/eeafb757b9dea629e9d4d719418170bffd4c0d041ea06292851fd09d/image/1234/240x300/110/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
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
