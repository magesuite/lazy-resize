<?php

$objectManager = Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
$connection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();

$connection->update(
    $connection->getTableName('catalog_product_entity_media_gallery'),
    ['file_size' => 1234],
    ['value = ?' => '/m/a/magento_image.jpg']
);

