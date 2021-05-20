<?php

namespace MageSuite\LazyResize\Plugin\DataFeedManager\Model\ResourceModel\Product;

class Collection
{
    public function afterGetMainRequest(
        \Wyomind\DataFeedManager\Model\ResourceModel\Product\Collection $subject,
        \Wyomind\DataFeedManager\Model\ResourceModel\Product\Collection $result
    ) {
        return $result->addMediaGalleryData();
    }
}