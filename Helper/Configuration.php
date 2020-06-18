<?php

namespace MageSuite\LazyResize\Helper;

class Configuration
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function shouldIncludeImageFileSizeInUrl() {
        return (bool)$this->scopeConfig->getValue('images/url_generation/include_image_file_size_in_url');
    }


    public function isOptimizationEnabled() {
        return (bool)$this->scopeConfig->getValue('images/images_optimization/enable_optimization');
    }

    public function getOptimizationLevel() {
        return $this->scopeConfig->getValue('dev/images_optimization/images_optimization_level');
    }
}
