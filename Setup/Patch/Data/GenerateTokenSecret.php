<?php

declare(strict_types=1);

namespace MageSuite\LazyResize\Setup\Patch\Data;

class GenerateTokenSecret implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    public const XML_PATH_TOKEN_SECRET = 'dev/lazy_resize/token_secret';

    public function __construct(
        protected \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {}

    public function apply(): self
    {
        $secret = bin2hex(random_bytes(16));
        $this->configWriter->save(self::XML_PATH_TOKEN_SECRET, $secret);

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
