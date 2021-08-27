<?php

namespace MageSuite\LazyResize\Service\Magento;

class TokenSecretProvider implements \MageSuite\LazyResize\Api\TokenSecretProviderInterface
{
    /**
     * @var \MageSuite\LazyResize\Helper\Configuration
     */
    protected $configuration;

    public function __construct(\MageSuite\LazyResize\Helper\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getTokenSecret(): string
    {
        return $this->configuration->getTokenSecret();
    }
}
