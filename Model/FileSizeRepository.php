<?php

namespace MageSuite\LazyResize\Model;

class FileSizeRepository implements \MageSuite\LazyResize\Api\FileSizeRepositoryInterface
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection = null;

    /**
     * @var int[]
     */
    protected $fileSizes = [];

    /**
     * @var Cache\ClearCacheForProductsWithUpdatedImages
     */
    protected $clearCacheForProductsWithUpdatedImages;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \MageSuite\LazyResize\Model\Cache\ClearCacheForProductsWithUpdatedImages $clearCacheForProductsWithUpdatedImages
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->clearCacheForProductsWithUpdatedImages = $clearCacheForProductsWithUpdatedImages;
    }

    public function addFileSize($filePath, $fileSize)
    {
        $this->fileSizes[$filePath] = $fileSize;
    }

    public function getFileSize($filePath)
    {
        if (empty($filePath)) {
            return 0;
        }

        if (!isset($this->fileSizes[$filePath])) {
            $tableName = $this->connection->getTableName('catalog_product_entity_media_gallery');
            $select = $this->connection->select()
                ->from($tableName, 'file_size')
                ->where("`value` = :file_path")
                ->where('file_size > 0');
            $bind = ['file_path' => $filePath];
            $fileSize = (int) $this->connection->fetchOne($select, $bind);
            $this->fileSizes[$filePath] = $fileSize;
        }

        return $this->fileSizes[$filePath];
    }

    public function save($fileSizes)
    {
        $tableName = $this->connection->getTableName('catalog_product_entity_media_gallery');

        $paths = [];
        $conditions = [];

        foreach ($fileSizes as $fileSize) {
            $path = DIRECTORY_SEPARATOR . ltrim($fileSize->getPath(), DIRECTORY_SEPARATOR);

            $case = $this->connection->quoteInto('?', $path);
            $result = $this->connection->quoteInto('?', $fileSize->getSize());

            $paths[] = $path;
            $conditions[$case] = $result;
        }

        $value = $this->connection->getCaseSql('value', $conditions, 'file_size');
        $where = ['value IN (?)' => $paths];

        try {
            $this->connection->beginTransaction();
            $this->connection->update($tableName, ['file_size' => $value], $where);
            $this->connection->commit();

            $this->clearCacheForProductsWithUpdatedImages->execute($fileSizes);
        } catch (\Exception $e) {
            $this->connection->rollBack();
        }

        return $fileSizes;
    }
}
