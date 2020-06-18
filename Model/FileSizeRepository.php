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

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function addFileSize($filePath, $fileSize)
    {
        $this->fileSizes[$filePath] = $fileSize;
    }

    public function getFileSize($filePath)
    {
        return $this->fileSizes[$filePath] ?? 0;
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

        } catch(\Exception $e) {
            $this->connection->rollBack();
        }

        return $fileSizes;
    }
}
