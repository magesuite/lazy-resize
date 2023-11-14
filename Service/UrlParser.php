<?php

namespace MageSuite\LazyResize\Service;

class UrlParser
{
    public $urlParts = [
        3 => 'token',
        4 => 'type',
        5 => 'width_and_height',
        6 => 'boolean_flags',
        7 => 'optimization_level',
        8 => 'image_file'
    ];

    public function parseUrl($url) {
        $urlParts = explode('/', $url, 9);
        $urlParts = $this->getUrlParts($urlParts);

        $values = [];

        $values['type'] = $urlParts['type'];

        $values += $this->parseWidthAndHeight($urlParts['width_and_height']);
        $values += $this->parseBooleanFlags($urlParts['boolean_flags']);
        $values['image_file'] = '/'.$urlParts['image_file'];
        $values['token'] = $urlParts['token'];
        $values['optimization_level'] = $urlParts['optimization_level'];

        return $values;
    }

    protected function getUrlParts($urlParts) {
        $parts = [];

        foreach($this->urlParts as $partIndex => $partName) {
            $parts[$partName] = $urlParts[$partIndex];
        }

        return $parts;
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
}
