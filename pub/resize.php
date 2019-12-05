<?php

use Symfony\Component\HttpFoundation\Request;

if(!defined('BP')) {
    define('BP', realpath(__DIR__ . '/../'));
}

require_once BP . '/vendor/autoload.php';

/**
 * Class is defined only for purposes of DI compiler which complains when code is not wrapped in class in Magento module
 * Class ResizeApplication
 */
class ResizeApplication {

    public function run()
    {
        $request = Request::createFromGlobals();

        $imageUrlHandler = new \MageSuite\LazyResize\Service\ImageUrlHandler();

        $parameters = $imageUrlHandler->parseUrl();

        $controller = new \MageSuite\LazyResize\Controller\Resize(
            new \MageSuite\LazyResize\Service\TokenGenerator(),
            $imageUrlHandler,
            new \MageSuite\LazyResize\Service\ImageProcessor(
                new \MageSuite\ImageResize\Service\Image\Resize(),
                new \MageSuite\ImageOptimization\Service\Image\CommandLine\Optimizer(
                    new \ImageOptimizer\OptimizerFactory([
                        'jpegoptim_options' => ['--max=' . $parameters['optimization_level']],
                        'execute_only_first_jpeg_optimizer' => false,
                        'execute_only_first_png_optimizer' => false
                    ])
                )
            )
        );

        $controller->execute($request->getRequestUri())->send();
    }
}

/*
 * This endpoint must be executed only when invoked by HTTP request
 */
if(php_sapi_name() != 'cli') {
    $controller = new ResizeApplication();
    $controller->run();
}
