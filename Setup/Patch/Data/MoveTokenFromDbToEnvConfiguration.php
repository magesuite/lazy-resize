<?php

declare(strict_types=1);

namespace MageSuite\LazyResize\Setup\Patch\Data;

class MoveTokenFromDbToEnvConfiguration implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    public function __construct(
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
        protected \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        protected \Magento\Framework\App\DeploymentConfig\Writer $writer
    ) {}

    public function apply(): self
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('core_config_data'), ['value'])
            ->where('path = ?', \MageSuite\LazyResize\Setup\Patch\Data\GenerateTokenSecret::XML_PATH_TOKEN_SECRET);
        $tokenSecret = $connection->fetchOne($select);

        if (empty($tokenSecret)) {
            return $this;
        }

        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            ['path = ?' => \MageSuite\LazyResize\Setup\Patch\Data\GenerateTokenSecret::XML_PATH_TOKEN_SECRET]
        );

        if ($this->deploymentConfig->get('lazy_resize/secret')) {
            return $this;
        }

        $data = [
            'lazy_resize' => [
                'secret' => $tokenSecret
            ]
        ];
        $configData = [\Magento\Framework\Config\File\ConfigFilePool::APP_ENV => $data];
        $this->writer->saveConfig($configData, false);

        return $this;
    }

    public static function getDependencies(): array
    {
        return [
            \MageSuite\LazyResize\Setup\Patch\Data\GenerateTokenSecret::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}
