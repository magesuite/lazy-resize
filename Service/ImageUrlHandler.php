<?php

namespace MageSuite\LazyResize\Service;

class ImageUrlHandler
{
    /**
     * @var string $url
     */
    private $url = '/media/catalog/product/thumbnail/{token}/{type}/{width_and_height}/{boolean_flags}/{optimization_level}/{first_letter}/{second_letter}/{image_file_path}';

    /**
     * @var array $parts
     */
    private $parts = [
        'type' => '[a-z_]+',
        'width_and_height' => '([0-9]{1,4})x([0-9]{1,4})',
        'boolean_flags' => '[0|1]{3}',
        'optimization_level' => '[0-9]{1,3}',
        'token' => '[a-z0-9]{32}',
        'first_letter' => '[^/]',
        'second_letter' => '[^/]',
        'image_file_path' => '[^/]+'
    ];

    /**
     * @var \Symfony\Component\Routing\RouteCollection $routes
     */
    private $routes;

    /**
     * @var \Symfony\Component\Routing\RequestContext $requestContext
     */
    private $requestContext;

    public function __construct()
    {
        $route = new \Symfony\Component\Routing\Route(
            $this->url,
            [],
            $this->parts
        );

        $routes = new \Symfony\Component\Routing\RouteCollection();
        $routes->add('resize', $route);
        $this->routes = $routes;

        $context = new \Symfony\Component\Routing\RequestContext();
        /**
         * There's no need to have uploaded files data only for ULRs generation and mathing.
         * But without this Magento is throwing errors when tests with file upload are running
         */
        $_FILES = [];
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
        $urlParts['image_file'] = '/'. implode('/', [ $urlParts['first_letter'], $urlParts['second_letter'], $urlParts['image_file_path']]);

        return $urlParts;
    }

    public function generateUrl($configuration)
    {
        $tokenGenerator = new \MageSuite\LazyResize\Service\TokenGenerator();

        $configuration['width_and_height'] = $this->buildWidthAndHeight($configuration);
        $configuration['boolean_flags'] = $this->buildBooleanFlags($configuration);

        $configuration['token'] = $tokenGenerator->generate($configuration);

        $configuration['image_file'] = ltrim($configuration['image_file'], '/');

        $urlFileParts = explode('/', $configuration['image_file']);
        $urlFileParts = array_combine(['first_letter', 'second_letter', 'image_file_path'], $urlFileParts);

        $configuration += $urlFileParts;

        $parts = $this->parts;

        $configuration = array_filter(
            $configuration,
            function ($key) use ($parts) {
                return in_array($key, array_keys($parts));
            },
            ARRAY_FILTER_USE_KEY
        );

        $generator = new \Symfony\Component\Routing\Generator\UrlGenerator($this->routes, $this->requestContext);

        return str_replace('/media/', '', $generator->generate('resize', $configuration));
    }

    protected function parseWidthAndHeight($widthAndHeight) {
        list($width, $height) = explode('x', $widthAndHeight);

        return [
            'width' => intval($width),
            'height' => intval($height)
        ];
    }

    protected function parseBooleanFlags($booleanFlags) {
        $flags = [
            'aspect_ratio',
            'transparency',
            'enable_optimization'
        ];

        $values = [];

        for($index = 0; $index < strlen($booleanFlags); $index++) {
            $values[$flags[$index]] = $booleanFlags[$index] == '1' ? true : false;
        }

        return $values;
    }

    protected function buildWidthAndHeight($configuration) {
        $width = isset($configuration['width']) ? intval($configuration['width']) : 0;
        $height = isset($configuration['height']) ? intval($configuration['height']) : 0;

        return sprintf('%sx%s', $width, $height);
    }

    protected function buildBooleanFlags($configuration)
    {
        $flags = [
            'aspect_ratio',
            'transparency',
            'enable_optimization'
        ];

        $result = '';

        foreach($flags as $flagIdentifier) {
            if(isset($configuration[$flagIdentifier]) and $configuration[$flagIdentifier]) {
                $result .= '1';
                continue;
            }

            $result .= '0';
        }

        return $result;
    }
}