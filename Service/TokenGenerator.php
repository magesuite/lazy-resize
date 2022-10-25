<?php

namespace MageSuite\LazyResize\Service;

class TokenGenerator
{
    /**
     * @var \MageSuite\LazyResize\Api\TokenSecretProviderInterface
     */
    protected $tokenSecretProvider;

    protected static $secretToken = null;

    public function __construct(\MageSuite\LazyResize\Api\TokenSecretProviderInterface $tokenSecretProvider = null)
    {
        $this->tokenSecretProvider = $tokenSecretProvider ?: new \MageSuite\LazyResize\Service\Resize\TokenSecretProvider();
    }

    public function generate($configuration)
    {
        if (!self::$secretToken) {
            self::$secretToken = $this->tokenSecretProvider->getTokenSecret();
        }

        $key = self::$secretToken .
            sha1($configuration['type']) .
            $configuration['file_size'] .
            (int) $configuration['width'] .
            (int) $configuration['height'] .
            (int) $configuration['aspect_ratio'] .
            (int) $configuration['transparency'] .
            (int) $configuration['enable_optimization'] .
            $configuration['image_file'] .
            $configuration['optimization_level'];

        return hash('sha3-224', $key);
    }
}
