<?php
namespace Slothsoft\Blob;

use w3c\FileAPI\URL as UrlInterface;

class URL implements UrlInterface
{
    private static $blobCount = 0;
    private static $blobStore = [];
    public static function createObjectURL($blob): string {
        self::$blobCount++;
        $url = sprintf('blob://%d', self::$blobCount);
        self::$blobStore[$url] = $blob;
        return $url;
    }
    public static function revokeObjectURL(string $url) {
        unset(self::$blobStore[$url]);
    }
    public static function resolveObjectURL(string $url) {
        return self::$blobStore[$url] ?? null;
    }
}

