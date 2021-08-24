<?php

namespace MageSuite\LazyResize\Service;

class TokenGenerator
{
    /**
     * @var \MageSuite\LazyResize\Helper\Configuration
     */
    protected $configuration;

    public function __construct(\MageSuite\LazyResize\Helper\Configuration $configuration = null)
    {
        $this->configuration = $configuration ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\MageSuite\LazyResize\Helper\Configuration::class);
    }

    public function generate($configuration)
    {
        $key = $this->getSecret() .
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

    protected function getSecret()
    {
        return $this->configuration->getTokenSecret();
    }
}
