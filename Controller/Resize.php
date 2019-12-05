<?php

namespace MageSuite\LazyResize\Controller;

class Resize
{
    const PLACEHOLDER_LOCATION = '/media/placeholders/%s.png';

    /**
     * @var \MageSuite\LazyResize\Service\TokenGenerator $tokenGenerator
     */
    protected $tokenGenerator;

    /**
     * @var \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler
     */
    protected $imageUrlHandler;

    /**
     * @var \MageSuite\LazyResize\Service\ImageProcessor $imageProcessor
     */
    protected $imageProcessor;

    public function __construct(
        \MageSuite\LazyResize\Service\TokenGenerator $tokenGenerator,
        \MageSuite\LazyResize\Service\ImageUrlHandler $imageUrlHandler,
        \MageSuite\LazyResize\Service\ImageProcessor $imageProcessor
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->imageUrlHandler = $imageUrlHandler;
        $this->imageProcessor = $imageProcessor;
    }

    public function execute($requestUri)
    {
        $configuration = $this->imageUrlHandler->parseUrl();

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

            $resizedFilePath = $this->imageProcessor->save(str_replace('/media/', '', $requestUri));

            return new \Symfony\Component\HttpFoundation\Response(
                $this->imageProcessor->returnToBrowser($resizedFilePath),
                200,
                ['Content-Type' => $this->imageProcessor->getMimeType()]
            );
        } catch (\MageSuite\ImageResize\Exception\OriginalImageNotFound $exception) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                sprintf(self::PLACEHOLDER_LOCATION, $configuration['type'])
            );
        }
    }
}
