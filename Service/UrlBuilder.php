<?php

namespace MageSuite\LazyResize\Service;

class UrlBuilder
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(TokenGenerator $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function buildUrl($configuration) {
        $token = $this->tokenGenerator->generate($configuration);

        $imageFile = ltrim($configuration['image_file'], '/');

        $booleanFlags = $this->buildBooleanFlags($configuration);

        return sprintf(
            'catalog/product/thumbnail/%s/%s/%s/%s/%s/%s',
            $token,
            $configuration['type'],
            $this->getWidthAndHeight($configuration),
            $booleanFlags,
            $configuration['optimization_level'],
            $imageFile
        );
    }

    protected function getWidthAndHeight($configuration) {
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
