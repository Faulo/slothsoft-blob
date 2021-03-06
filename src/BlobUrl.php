<?php
declare(strict_types = 1);
namespace Slothsoft\Blob;

use w3c\FileAPI\URL;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class BlobUrl implements URL {

    const URL_SCHEME = 'blob';

    const URL_ORIGIN = '/';

    private static $blobCount = 0;

    private static $blobStore = [];

    public static function createObjectURL($blob): string {
        if (! is_resource($blob)) {
            throw new \InvalidArgumentException("resource expected, but got " . gettype($blob));
        }
        self::$blobCount ++;
        $url = self::buildObjectUrl(self::URL_SCHEME, self::URL_ORIGIN, (string) self::$blobCount);
        self::$blobStore[$url] = $blob;
        return $url;
    }

    public static function revokeObjectURL(string $url) {
        if (isset(self::$blobStore[$url])) {
            fclose(self::$blobStore[$url]);
            unset(self::$blobStore[$url]);
        }
    }

    public static function resolveObjectURL(string $url) {
        return self::$blobStore[$url] ?? null;
    }

    public static function createTemporaryObject() {
        return fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
    }

    public static function createTemporaryURL(): string {
        return self::createObjectURL(self::createTemporaryObject());
    }

    /**
     *
     * @see https://w3c.github.io/FileAPI/#url-model
     */
    private static function buildObjectUrl(string $scheme, string $origin, string $uuid): string {
        return "$scheme:$origin/$uuid";
    }
}

