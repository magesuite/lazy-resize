<?php

namespace MageSuite\LazyResize\Helper;

class Configuration
{
    const XML_PATH_ENABLE_OPTIMIZATION = 'images/images_optimization/enable_optimization';
    const XML_PATH_OPTIMIZATION_LEVEL = 'dev/images_optimization/images_optimization_level';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isOptimizationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_OPTIMIZATION);
    }

    public function getOptimizationLevel()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPTIMIZATION_LEVEL, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}
