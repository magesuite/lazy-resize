<?php

namespace MageSuite\LazyResize\Test\Integration;

class TokenSecretHelper
{
    public function prepareTokenSecretForTests()
    {
        $cacheFilePath = BP . '/var/global/lazy_resize_secret';

        if (!file_exists($cacheFilePath)) { //phpcs:ignore
            return;
        }

        file_put_contents($cacheFilePath, (string)\MageSuite\LazyResize\Helper\Configuration::DEFAULT_TOKEN_SECRET); //phpcs:ignore
    }
}
