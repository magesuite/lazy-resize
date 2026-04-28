# MageSuite LazyResize

Magento 2 extension for lazy (on-demand) image resizing.

Documentation: https://creativestyle.atlassian.net/wiki/spaces/MGSDEV/pages/2358640724/LazyResize

### Required entry in `app/etc/env.php`

Add a `lazy_resize` section to the array returned by `app/etc/env.php`:

```php
return [
    // ... other Magento configuration ...

    'lazy_resize' => [
        'secret' => 'your-secret-value-here'
    ],
];
```