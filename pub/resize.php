<?php
// phpcs:ignoreFile

if (!defined('BP')) {
    define('BP', realpath(__DIR__ . '/../'));
}

require_once BP . '/vendor/autoload.php';

/**
 * Class is defined only for purposes of DI compiler which complains when code is not wrapped in class in Magento module
 * Class ResizeApplication
 */
class ResizeApplication
{

    public function run()
    {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        $imageUrlHandler = new \MageSuite\LazyResize\Service\ImageUrlHandler();

        $parameters = $imageUrlHandler->parseUrl();
        if ($parameters instanceof \Symfony\Component\HttpFoundation\Response && $parameters->isNotFound()) {
            header('HTTP/1.0 404 Not Found'); //phpcs:ignore
            exit; //phpcs:ignore
        }

        $controller = new \MageSuite\LazyResize\Controller\Resize(
            new \MageSuite\LazyResize\Service\TokenGenerator(),
            $imageUrlHandler,
            new \MageSuite\LazyResize\Service\ImageProcessor(
                new \MageSuite\ImageResize\Service\Image\Resize(
                    new \MageSuite\ImageResize\Repository\File(),
                    new \MageSuite\ImageResize\Service\Image\Watermark(new \MageSuite\ImageResize\Repository\File())
                )
            )
        );

        $controller->execute($request->getRequestUri())->send();
    }
}

/*
 * This endpoint must be executed only when invoked by HTTP request
 */
if (php_sapi_name() != 'cli') {
    $controller = new ResizeApplication();
    $controller->run();
}
