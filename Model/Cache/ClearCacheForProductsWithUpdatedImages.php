<?php

namespace MageSuite\LazyResize\Model\Cache;

class ClearCacheForProductsWithUpdatedImages
{
    const BATCH_SIZE = 500;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection = null;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    protected $cacheContext;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Indexer\CacheContext $cacheContext
    )
    {
        $this->connection = $resourceConnection->getConnection();
        $this->cache = $cache;
        $this->eventManager = $eventManager;
        $this->cacheContext = $cacheContext;
    }

    public function execute($fileSizes) {
        if(empty($fileSizes)) {
            return;
        }

        $batches = array_chunk($fileSizes, self::BATCH_SIZE);

        foreach($batches as $batch) {
            $this->clearCacheForBatchOfImages($batch);
        }
    }

    protected function clearCacheForBatchOfImages($batch)
    {
        $paths = array_map(function($item) {
            return $item->getPath();
        }, $batch);

        $productIds = $this->getAffectedProductsIds($paths);

        if(empty($productIds)) {
            return;
        }

        $tags = $this->generateCacheTags($productIds);

        $this->cache->clean($tags);
        $this->cacheContext->registerTags($tags);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
    }

    protected function getAffectedProductsIds($imagesPaths) {
        $galleryValueToEntityTable = $this->connection->getTableName('catalog_product_entity_media_gallery_value_to_entity');
        $galleryTable = $this->connection->getTableName('catalog_product_entity_media_gallery');

        $subSelect = $this->connection->select();
        $subSelect->from($galleryTable, 'value_id')
            ->where('value IN (?)', $imagesPaths);

        $select = $this->connection->select();
        $select->from($galleryValueToEntityTable, 'entity_id')
            ->where('value_id IN (?)', new \Zend_Db_Expr($subSelect));

        return $this->connection->fetchCol($select);
    }

    protected function generateCacheTags(array $productIds)
    {
        $tags = [];

        foreach($productIds as $productId) {
            $tags[] = \Magento\Catalog\Model\Product::CACHE_TAG . '_' . $productId;
        }

        return $tags;
    }
}
