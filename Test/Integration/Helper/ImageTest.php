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
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \MageSuite\LazyResize\Helper\Image
     */
    protected $imageHelper;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->imageHelper = $this->objectManager->get(\MageSuite\LazyResize\Helper\Image::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productCollectionFactory = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class);
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

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/bc3285e97c14764d19e320531a0b1c355d9f418f4aae26b1f6393de6/small_image/1234/240x300/000/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWhenImageIsDefinedOnProductFromCollection()
    {
        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/bc3285e97c14764d19e320531a0b1c355d9f418f4aae26b1f6393de6/small_image/1234/240x300/000/0/m/a/magento_image.jpg';

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $product = $collection->addFieldToFilter('sku', 'simple')->addAttributeToSelect('small_image')->getFirstItem();
        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);
        $this->assertEquals($expectedUrl, $url);

        $collectionWithMediaGalleryData = $this->productCollectionFactory->create();
        $productWithMediaGalleryData = $collection->addFieldToFilter('sku', 'simple')->addMediaGalleryData()->getFirstItem();
        $url = $this->getImageUrl($productWithMediaGalleryData);
        $url = str_replace('pub/', '', $url);
        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testItReturnsProperUrlWithFileSizeWhenImageIsDefined()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/bc3285e97c14764d19e320531a0b1c355d9f418f4aae26b1f6393de6/small_image/1234/240x300/000/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/dev/images_optimization/images_optimization_level 60
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testIfImageUrlHaveChangedOptimizationLevelParam()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/b77e1ccfe001590131245ef0e76004e82a900a7393691e68a4cdbbba/small_image/1234/240x300/000/60/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/dev/images_optimization/images_optimization_level 60
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testIfImageUrlHaveChangedOptimizationLevelParamAndFileSize()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/b77e1ccfe001590131245ef0e76004e82a900a7393691e68a4cdbbba/small_image/1234/240x300/000/60/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/images/images_optimization/enable_optimization 1
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testIfImageUrlHaveEnabledImageOptimization()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/fcfb40765f8d490a144a688f5a1f11714b76ba9e5bf740a7fc5036fb/small_image/1234/240x300/001/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/images/images_optimization/enable_optimization 1
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     * @magentoDataFixture setFileSize
     */
    public function testIfImageUrlHaveEnabledImageOptimizationAndFileSize()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/fcfb40765f8d490a144a688f5a1f11714b76ba9e5bf740a7fc5036fb/small_image/1234/240x300/001/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testItReturnsPlaceholderUrlWhenImageIsNotDefined()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = $this->prepareRegexUrl('http://localhost/static/version([0-9]+?)/frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg');

        $assertRegExp = method_exists($this, 'assertMatchesRegularExpression') ? 'assertMatchesRegularExpression' : 'assertRegExp';

        $this->$assertRegExp($expectedUrl, $url);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture current_store catalog/placeholder/small_image_placeholder default/placeholder.jpg
     */
    public function testItReturnsCustomPlaceholderUrlWhenItIsDefined()
    {
        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/placeholder/default/placeholder.jpg';

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
        $_SERVER['ORIGINAL_URI'] = '/index.php'; //phpcs:ignore
        $_SERVER['REQUEST_URI'] = '/index.php'; //phpcs:ignore

        $product = $this->productRepository->get('simple');

        $url = $this->getImageUrl($product);
        $url = str_replace('pub/', '', $url);

        $expectedUrl = 'http://localhost/media/catalog/product/thumbnail/bc3285e97c14764d19e320531a0b1c355d9f418f4aae26b1f6393de6/small_image/1234/240x300/000/0/m/a/magento_image.jpg';

        $this->assertEquals($expectedUrl, $url);
    }

    protected function prepareRegexUrl($url)
    {
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

    public static function setFileSize()
    {
        require __DIR__ . '/../_files/file_size.php';
    }
}
