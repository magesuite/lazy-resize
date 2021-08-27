<?php

namespace MageSuite\LazyResize\Service;

class TokenGenerator
{
    /**
     * @var \MageSuite\LazyResize\Api\TokenSecretProviderInterface
     */
    protected $tokenSecretProvider;

    public function __construct(\MageSuite\LazyResize\Api\TokenSecretProviderInterface $tokenSecretProvider = null)
    {
        $this->tokenSecretProvider = $tokenSecretProvider ?: new \MageSuite\LazyResize\Service\Resize\TokenSecretProvider();
    }

    public function generate($configuration)
    {
        $key = $this->tokenSecretProvider->getTokenSecret() .
            sha1($configuration['type']) .
            $configuration['file_size'] .
            $configuration['width'] .
            $configuration['height'] .
            $configuration['aspect_ratio'] .
            $configuration['transparency'] .
            $configuration['enable_optimization'] .
            $configuration['image_file'] .
            $configuration['optimization_level'];

        return hash('sha3-224', $key);
    }
}
