<?php

namespace MageSuite\LazyResize\Model;

class FileSizeRepository
{
    protected $fileSizes = [];

    public function addFileSize($filePath, $fileSize) {
        $this->fileSizes[$filePath] = $fileSize;
    }

    public function getFileSize($filePath) {
        return $this->fileSizes[$filePath] ?? 0;
    }
}
