<?php

namespace MageSuite\LazyResize\Service;

class ImageUrlHandler
{
    protected \Symfony\Component\Routing\RouteCollection $routes;
    protected \Symfony\Component\Routing\RequestContext $requestContext;

    protected string $url = '/media/catalog/product/thumbnail/{token}/{type}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}';
    protected string $urlWatermark = '/media/catalog/product/thumbnail/{token}/{type}/w-{watermark}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}'; // phpcs:ignore

    protected string $urlWithFileSize = '/media/catalog/product/thumbnail/{token}/{type}/{file_size}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}'; // phpcs:ignore
    protected string $urlWithFileSizeWatermark = '/media/catalog/product/thumbnail/{token}/{type}/w-{watermark}/{file_size}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}'; // phpcs:ignore

    protected array $parts = [
        'type' => '[a-z_]+',
        'width_and_height' => '([0-9]{1,4})x([0-9]{1,4})',
        'boolean_flags' => '[0|1]{3}',
        'optimization_level' => '[0-9]{1,3}',
        'file_size' => '[0-9]{1,12}',
        'token' => '[a-z0-9]{56}',
        'first_letter' => '[^/]',
        'second_letter' => '[^/]',
        'image_file_path' => '[^/]+',
        'watermark' => '[A-Za-z0-9_-]+',
    ];

    public function __construct()
    {
        $routes = new \Symfony\Component\Routing\RouteCollection();

        $route = new \Symfony\Component\Routing\Route(
            $this->url,
            [],
            $this->parts
        );

        $routes->add('resize', $route);

        $route = new \Symfony\Component\Routing\Route(
            $this->urlWatermark,
            [],
            $this->parts
        );

        $routes->add('resize_watermark', $route);

        $route = new \Symfony\Component\Routing\Route(
            $this->urlWithFileSize,
            [],
            $this->parts
        );

        $routes->add('resize_with_file_size', $route);

        $route = new \Symfony\Component\Routing\Route(
            $this->urlWithFileSizeWatermark,
            [],
            $this->parts
        );

        $routes->add('resize_with_file_size_watermark', $route);

        $this->routes = $routes;

        $context = new \Symfony\Component\Routing\RequestContext();
        /**
         * There's no need to have uploaded files data only for ULRs generation and mathing.
         * But without this Magento is throwing errors when tests with file upload are running
         */
        $_FILES = []; //phpcs:ignore
        $context->fromRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
        $this->requestContext = $context;
    }

    public function parseUrl()
    {
        return $this->matchUrl($this->requestContext->getPathInfo());
    }

    public function matchUrl($url)
    {
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($this->routes, $this->requestContext);

        try {
            $urlParts = $matcher->match($url);
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $exception) {
            return new \Symfony\Component\HttpFoundation\Response(
                '',
                404
            );
        }

        $urlParts += $this->parseWidthAndHeight($urlParts['width_and_height']);
        $urlParts += $this->parseBooleanFlags($urlParts['boolean_flags']);
        $urlParts['image_file'] = '/' . implode('/', [ $urlParts['first_letter'], $urlParts['second_letter'], $urlParts['image_file_path']]);

        return $urlParts;
    }

    public function generateUrl($configuration)
    {
        $tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator();
        $routeName = 'resize_with_file_size';

        if (!isset($configuration['file_size'])) {
            $configuration['file_size'] = 0;
        }

        $configuration['width_and_height'] = $this->buildWidthAndHeight($configuration);
        $configuration['boolean_flags'] = $this->buildBooleanFlags($configuration);
        $configuration['token'] = $tokenGenerator->generate($configuration);
        $configuration['image_file'] = ltrim($configuration['image_file'], '/');

        $urlFileParts = explode('/', $configuration['image_file']);
        $urlFileParts = array_combine(['first_letter', 'second_letter', 'image_file_path'], $urlFileParts);

        $configuration += $urlFileParts;

        $watermark = $configuration['watermark'] ?? null;

        if (!$watermark?->isValid()) {
            unset($configuration['watermark']);
        }

        $parts = $this->parts;

        $configuration = array_filter(
            $configuration,
            function ($key) use ($parts) { //phpcs:ignore
                return in_array($key, array_keys($parts));
            },
            ARRAY_FILTER_USE_KEY
        );

        $generator = new \Symfony\Component\Routing\Generator\UrlGenerator($this->routes, $this->requestContext);

        if (!empty($configuration['watermark'])) {
            $routeName = 'resize_with_file_size_watermark';
        }

        return preg_replace('/(\/index.php)?\/media\//i', '', $generator->generate($routeName, $configuration));
    }

    protected function parseWidthAndHeight($widthAndHeight): array
    {
        list($width, $height) = explode('x', $widthAndHeight);

        return [
            'width' => (int)$width,
            'height' => (int)$height
        ];
    }

    protected function parseBooleanFlags($booleanFlags): array
    {
        $flags = [
            'aspect_ratio',
            'transparency',
            'enable_optimization'
        ];

        $values = [];
        $booleanFlagsLength = strlen($booleanFlags);

        for ($index = 0; $index < $booleanFlagsLength; $index++) {
            $values[$flags[$index]] = $booleanFlags[$index] == '1' ? 1 : 0;
        }

        return $values;
    }

    protected function buildWidthAndHeight($configuration): string
    {
        $width = isset($configuration['width']) ? (int)$configuration['width'] : 0;
        $height = isset($configuration['height']) ? (int)$configuration['height'] : 0;

        return sprintf('%sx%s', $width, $height);
    }

    protected function buildBooleanFlags($configuration): string
    {
        $flags = [
            'aspect_ratio',
            'transparency',
            'enable_optimization'
        ];

        $result = '';

        foreach ($flags as $flagIdentifier) {
            if (isset($configuration[$flagIdentifier]) && $configuration[$flagIdentifier]) {
                $result .= 1;
                continue;
            }

            $result .= 0;
        }

        return $result;
    }
}
