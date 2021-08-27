<?php

namespace MageSuite\LazyResize\Service\Resize;

class TokenSecretProvider implements \MageSuite\LazyResize\Api\TokenSecretProviderInterface
{
    const DAY_IN_SECONDS = 24 * 60 * 60;

    public function getTokenSecret(): string
    {
        $cacheFilePath = $this->getCacheFilePath();

        $secret = @file_get_contents($cacheFilePath); //phpcs:ignore

        if (empty($secret) || !$this->validateFileModificationTime($cacheFilePath)) {
            $secret = $this->getSecretFromDatabase();
            file_put_contents($cacheFilePath, (string)$secret); //phpcs:ignore
        }

        return $secret;
    }

    protected function getCacheFilePath()
    {
        return BP . '/var/global/lazy_resize_secret';
    }

    protected function validateFileModificationTime($cacheFilePath): bool
    {
        $currentTime = time();
        $fileModificationTime = filemtime($cacheFilePath); //phpcs:ignore

        if ($currentTime - ($fileModificationTime + self::DAY_IN_SECONDS) < 0) {
            return true;
        }

        return false;
    }

    protected function getSecretFromDatabase()
    {
        if (!$this->validateEnvFilePath()) {
            return \MageSuite\LazyResize\Helper\Configuration::DEFAULT_TOKEN_SECRET;
        }

        $databaseConfig = $this->getDatabaseConfig();
        $tableName = $databaseConfig['table_prefix'] . 'core_config_data';

        $connection = $this->getConnection($databaseConfig);

        $stmt = $connection->prepare("SELECT value FROM $tableName WHERE path = :path");
        $stmt->execute(['path' => \MageSuite\LazyResize\Helper\Configuration::XML_PATH_TOKEN_SECRET]);

        $secret = $stmt->fetchColumn();

        return $secret ?: \MageSuite\LazyResize\Helper\Configuration::DEFAULT_TOKEN_SECRET;
    }

    protected function validateEnvFilePath()
    {
        $path = BP . '/app/etc/env.php';

        if (file_exists($path)) { //phpcs:ignore
            return true;
        }

        return false;
    }

    protected function getConnection($databaseConfig)
    {
        $dsn = sprintf(
            'mysql:dbname=%s;host=%s',
            $databaseConfig['credentials']['dbname'],
            $databaseConfig['credentials']['host']
        );

        return new \PDO($dsn, $databaseConfig['credentials']['username'], $databaseConfig['credentials']['password']);
    }

    private function getDatabaseConfig(): array
    {
        $config = require_once BP . '/app/etc/env.php'; //phpcs:ignore

        return [
            'credentials' => $config['db']['connection']['default'],
            'table_prefix' => $config['db']['table_prefix']
        ];
    }
}
