<?php

namespace MageSuite\LazyResize\Test\Integration\Helper;

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
     * @var \MageSuite\LazyResize\Helper\Image
     */
    protected $imageHelper;

    public function setUp() {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->imageHelper = $this->objectManager->get(\MageSuite\LazyResize\Helper\Image::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testItReturnsProperUrlWhenImageIsDefined() {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);

        $expectedUrl = 'http://localhost/pub/media/catalog/product/thumbnail/4b480ef5debc72f2bd51472055f12d23/small_image/240x300/000/80/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/dev/images_optimization/images_optimization_level 60
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testIfImageUrlHaveChangedOptimizationLevelParam()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);

        $expectedUrl = 'http://localhost/pub/media/catalog/product/thumbnail/de8d74deccd33278c499dbcf695ec235/small_image/240x300/000/60/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/images/images_optimization/enable_optimization 1
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testIfImageUrlHaveEnabledImageOptimization()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);

        $expectedUrl = 'http://localhost/pub/media/catalog/product/thumbnail/92a6ebf6462b719fcc9c781e7697e4ca/small_image/240x300/001/80/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testItReturnsPlaceholderUrlWhenImageIsNotDefined() {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);

        $expectedUrl = $this->prepareRegexUrl('http://localhost/pub/static/version([0-9]+?)/frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg');

        $this->assertRegExp($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture current_store catalog/placeholder/small_image_placeholder default/placeholder.jpg
     */
    public function testItReturnsCustomPlaceholderUrlWhenItIsDefined() {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);

        $expectedUrl = 'http://localhost/pub/media/catalog/product/placeholder/default/placeholder.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    protected function prepareRegexUrl($url) {
        $url = str_replace('/', '\/', $url);
        return sprintf('/%s/', $url);
    }

    /**
     * @param $product
     * @return string
     */
    protected function getImageUrl($product)
    {
        $this->imageHelper->init($product, 'category_page_grid', []);

        return $this->imageHelper->getUrl();
    }
}
