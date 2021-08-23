<?php

namespace MageSuite\LazyResize\Setup\Patch\Data;

class GenerateTokenSecret implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    public function __construct(\Magento\Framework\App\Config\Storage\WriterInterface $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    public function apply()
    {
        $secret = bin2hex(random_bytes(16));

        $this->configWriter->save(\MageSuite\LazyResize\Helper\Configuration::XML_PATH_TOKEN_SECRET, $secret);
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
