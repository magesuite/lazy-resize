<?php

namespace MageSuite\LazyResize\Model;

class FileSizeRepository implements \MageSuite\LazyResize\Api\FileSizeRepositoryInterface
{
    const FOLDER_DELIMITER = '/';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection = null;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    protected $fileSizes = [];

    public function addFileSize($filePath, $fileSize)
    {
        $this->fileSizes[$filePath] = $fileSize;
    }

    public function getFileSize($filePath)
    {
        return $this->fileSizes[$filePath] ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function save($fileSizes)
    {
        $tableName = $this->connection->getTableName('catalog_product_entity_media_gallery');

        foreach ($fileSizes as $fileSize) {
            $path = self::FOLDER_DELIMITER . ltrim($fileSize->getPath(), self::FOLDER_DELIMITER);

            $this->connection->update(
                $tableName,
                ['file_size' => $fileSize->getSize()],
                ['value = ?' => $path]
            );
        }

        return $fileSizes;
    }
}
