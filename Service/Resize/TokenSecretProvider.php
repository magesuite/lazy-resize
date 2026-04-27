<?php

declare(strict_types=1);

namespace MageSuite\LazyResize\Service\Resize;

class TokenSecretProvider implements \MageSuite\LazyResize\Api\TokenSecretProviderInterface
{
    protected ?array $configuration = null;

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function getTokenSecret(): string
    {
        if (PHP_SAPI === 'cli') {
            $cacheFilePath = $this->getCacheFilePath();
            $secret = @file_get_contents($cacheFilePath); //phpcs:ignore

            if (!empty($secret)) {
                return $secret;
            }
        }

        $config = $this->getConfiguration();

        if (isset($config['lazy_resize']['secret'])) {
            return $config['lazy_resize']['secret'];
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('Lazy Resize secret is not defined')
        );
    }

    protected function getConfigFilePath(): string
    {
        return BP . '/app/etc/env.php';
    }

    protected function getCacheFilePath(): string
    {
        return BP . '/var/global/lazy_resize_secret';
    }

    protected function getConfiguration(): array
    {
        if ($this->configuration !== null) {
            return $this->configuration;
        }

        $this->configuration = include $this->getConfigFilePath(); //phpcs:ignore

        return $this->configuration;
    }

    protected function validateEnvFilePath(): bool
    {
        return file_exists($this->getConfigFilePath()); //phpcs:ignore
    }
}
