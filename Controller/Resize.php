<?php

namespace MageSuite\LazyResize\Controller;

class Resize
{
    const PLACEHOLDER_LOCATION = '/media/placeholders/%s.png';
    
    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var \MageSuite\LazyResize\Service\UrlParser
     */
    private $urlParser;
    /**
     * @var \MageSuite\LazyResize\Service\ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        \MageSuite\LazyResize\Service\TokenGenerator $tokenGenerator,
        \MageSuite\LazyResize\Service\UrlParser $urlParser,
        \MageSuite\LazyResize\Service\ImageProcessor $imageProcessor
    )
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->urlParser = $urlParser;
        $this->imageProcessor = $imageProcessor;
    }

    public function execute($requestUri) {
        $configuration = $this->urlParser->parseUrl($requestUri);

        if ($configuration['token'] != $this->tokenGenerator->generate($configuration)) {
            return new \Symfony\Component\HttpFoundation\Response(
                '',
                404
            );
        }

        try {
            $this->imageProcessor->process($configuration);

            if (isset($configuration['enable_optimization']) && $configuration['enable_optimization'] !== false) {
                $this->imageProcessor->optimize($configuration);
            }

            $resizedFilePath = $this->imageProcessor->save($requestUri);

            return new \Symfony\Component\HttpFoundation\Response(
                $this->imageProcessor->returnToBrowser($resizedFilePath),
                200,
                ['Content-Type' => $this->imageProcessor->getMimeType()]
            );
        }
        catch(\MageSuite\LazyResize\Exception\OriginalImageNotFound $exception) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                sprintf(self::PLACEHOLDER_LOCATION, $configuration['type'])
            );
        }
    }
}