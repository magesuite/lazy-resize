<?php

namespace MageSuite\LazyResize\Plugin\Catalog\Model\ResourceModel\Product\Gallery;

class AddFileSizeColumnToSqlQuery
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $subject
     * @param $result \Magento\Framework\DB\Select
     * @return mixed
     */
    public function afterCreateBatchBaseSelect(\Magento\Catalog\Model\ResourceModel\Product\Gallery $subject, $result)
    {
        $columns = $result->getPart(\Zend_Db_Select::COLUMNS);

        $columns[] = [
            'main',
            'file_size',
            'file_size',
        ];

        $result->setPart(\Zend_Db_Select::COLUMNS, $columns);

        return $result;
    }
}
