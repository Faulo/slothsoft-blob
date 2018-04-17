<?php
declare(strict_types = 1);
namespace Slothsoft\Blob;

use Slothsoft\Core\StreamWrapper\StreamWrapperFactoryInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Slothsoft\Core\StreamWrapper\ResourceStreamWrapper;

class BlobStreamWrapperFactory implements StreamWrapperFactoryInterface
{

    public function statUrl(string $url, int $flags)
    {
        if ($stream = self::createStreamWrapper($url, StreamWrapperInterface::MODE_OPEN_READONLY, 0)) {
            return $stream->stream_stat();
        } else {
            return false;
        }
    }

    /**
     * @return StreamWrapperInterface|null
     */
    public function createStreamWrapper(string $url, string $mode, int $options)
    {
        if ($resource = BlobUrl::resolveObjectURL($url)) {
            return new ResourceStreamWrapper($resource);
        } else {
            return null;
        }
    }
}

