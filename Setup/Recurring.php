<?php

namespace MageSuite\LazyResize\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        if (!defined('BP') || !is_writeable(BP . '/pub')) { //phpcs:ignore
            return;
        }

        copy(__DIR__ . '/../pub/resize.php', BP . '/pub/resize.php'); //phpcs:ignore
    }
}
