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
        if ($stream = self::createStreamWrapper($url, 'r', 0)) {
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
        if ($resource = URL::resolveObjectURL($url)) {
            switch ($mode[0]) {
                case 'r':
                case 'w':
                case 'x':
                case 'c':
                    fseek($resource, 0, SEEK_SET);
                    break;
                case 'a':
                    fseek($resource, 0, SEEK_END);
                    break;
                default:
                    return false;
            }
            return new ResourceStreamWrapper($resource);
        } else {
            return null;
        }
    }
}

