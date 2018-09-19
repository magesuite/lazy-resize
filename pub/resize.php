<?php

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
        $app = new Silex\Application();

        $app->get('/media/catalog/product/thumbnail/{token}/{type}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}',
            function (\Symfony\Component\HttpFoundation\Request $request) {
                $requestUri = $request->getRequestUri();
                $requestUri = ltrim($requestUri, '/media/');

                $level = $request->get('optimization_level');

                $controller = new \MageSuite\LazyResize\Controller\Resize(
                    new \MageSuite\LazyResize\Service\TokenGenerator(),
                    new \MageSuite\LazyResize\Service\UrlParser(),
                    new \MageSuite\LazyResize\Service\ImageProcessor(
                        new \MageSuite\LazyResize\Repository\File(),
                        new \MageSuite\Frontend\Service\Image\CommandLine\Optimizer(
                            new \ImageOptimizer\OptimizerFactory([
                                'jpegoptim_options' => ['--max=' . $level],
                                'execute_only_first_jpeg_optimizer' => false,
                                'execute_only_first_png_optimizer' => false
                            ])
                        )
                    )
                );

                return $controller->execute($requestUri);
            })
            ->assert('type', '[a-z_]+')
            ->assert('width_and_height', '([0-9]{2,4})x([0-9]{2,4})')
            ->assert('boolean_flags', '[0|1]{3}')
            ->assert('optimization_level', '[0-9]{1,3}')
            ->assert('token', '[a-z0-9]{32}')
            ->assert('first_letter', '[^/]')
            ->assert('second_letter', '[^/]')
            ->assert('image_file_path', '[^/]+');

        $app->run();
    }
}

/*
 * This endpoint must be executed only when invoked by HTTP request
 */
if(php_sapi_name() != 'cli') {
    $controller = new ResizeApplication();
    $controller->run();
}
