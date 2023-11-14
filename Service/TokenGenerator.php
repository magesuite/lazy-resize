<?php

namespace MageSuite\LazyResize\Service;

class TokenGenerator
{
    const SECRET = 'f8f9fb44b4d7c6fe7ecef7091d475170';

    public function generate($configuration)
    {
        return md5(
            $this->getSecret() .
            sha1($configuration['type']) .
            $configuration['width'] .
            $configuration['height'] .
            $configuration['aspect_ratio'] .
            $configuration['transparency'] .
            $configuration['enable_optimization'] .
            substr($configuration['image_file'], 1, 1) .
            substr($configuration['image_file'], 1, 3) .
            $configuration['optimization_level']
        );
    }

    protected function getSecret() {
        return self::SECRET;
    }
}
