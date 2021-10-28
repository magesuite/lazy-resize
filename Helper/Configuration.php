<?php

namespace MageSuite\LazyResize\Helper;

class Configuration
{
    const XML_PATH_ENABLE_OPTIMIZATION = 'images/images_optimization/enable_optimization';
    const XML_PATH_OPTIMIZATION_LEVEL = 'dev/images_optimization/images_optimization_level';
    const XML_PATH_TOKEN_SECRET = 'dev/lazy_resize/token_secret';

    const DEFAULT_TOKEN_SECRET = 'f8f9fb44b4d7c6fe7ecef7091d475170';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isOptimizationEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_OPTIMIZATION);
    }

    public function getOptimizationLevel()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPTIMIZATION_LEVEL, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getTokenSecret()
    {
        $secret = $this->scopeConfig->getValue(self::XML_PATH_TOKEN_SECRET);

        return $secret ?: self::DEFAULT_TOKEN_SECRET;
    }
}
