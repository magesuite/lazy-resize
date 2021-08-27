<?php

namespace MageSuite\LazyResize\Api;

interface TokenSecretProviderInterface
{
    /**
     * @return string
     */
    public function getTokenSecret(): string;
}
