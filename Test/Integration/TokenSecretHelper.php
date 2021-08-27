<?php

namespace MageSuite\LazyResize\Test\Integration;

class TokenSecretHelper
{
    public function prepareTokenSecretForTests()
    {
        $cacheFilePath = BP . '/var/global/lazy_resize_secret';
        file_put_contents($cacheFilePath, (string)\MageSuite\LazyResize\Helper\Configuration::DEFAULT_TOKEN_SECRET); //phpcs:ignore
    }
}
