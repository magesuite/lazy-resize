<?php

declare(strict_types=1);

namespace MageSuite\LazyResize\Test\Integration;

class TokenSecretHelper
{
    public const DEFAULT_TOKEN_SECRET = 'f8f9fb44b4d7c6fe7ecef7091d475170';

    public function prepareTokenSecretForTests(): void
    {
        $cacheFilePath = BP . '/var/global/lazy_resize_secret';

        if (file_exists($cacheFilePath)) { //phpcs:ignore
            return;
        }

        $directory = dirname($cacheFilePath); //phpcs:ignore

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true); //phpcs:ignore
        }

        file_put_contents($cacheFilePath, (string)self::DEFAULT_TOKEN_SECRET); //phpcs:ignore
    }
}
