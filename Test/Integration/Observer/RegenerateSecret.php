<?php

declare(strict_types=1);

namespace MageSuite\LazyResize\Test\Integration\Observer;

class RegenerateSecret implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        protected \MageSuite\LazyResize\Test\Integration\TokenSecretHelper $tokenSecretHelper
    ) {
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $this->tokenSecretHelper->prepareTokenSecretForTests();
    }
}
