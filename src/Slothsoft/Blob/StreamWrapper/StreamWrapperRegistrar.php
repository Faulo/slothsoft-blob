<?php
namespace Slothsoft\Blob\StreamWrapper;

class StreamWrapperRegistrar implements StreamWrapperInterface
{
    private static $factories = [];
    private static function addFactory(string $scheme, StreamWrapperFactoryInterface $factory) {
        self::$factories[$scheme] = $factory;
    }
    private static function getFactoryByScheme(string $scheme) : StreamWrapperFactoryInterface {
        return self::$factories[$scheme] ?? null;
    }
    private static function getFactoryByUrl(string $url) : StreamWrapperFactoryInterface {
        return self::getFactoryByScheme(parse_url($url, PHP_URL_SCHEME));
    }
    
    public static function registerStreamWrapper(string $scheme, StreamWrapperFactoryInterface $factory) {
        self::addFactory($scheme, $factory);
        stream_wrapper_register($scheme, self::class);
    }
    
    
    
    public function url_stat(string $url, int $flags)
    {
        return self::getFactoryByUrl($url)->statUrl($url, $flags);
    }
    
    public function stream_open(string $path, string $mode, int $options, &$opened_path)
    {
        $this->stream = self::getFactoryByUrl($path)->createStreamWrapper($path, $mode, $options);
        
        if ($this->stream === null) {
            return false;
        }
        
        return true;
    }
    
    
    //StreamWrapperInterface
    public function stream_stat(): array
    {
        return $this->stream->stream_stat();
    }
    public function stream_read(int $count): string
    {
        return $this->stream->stream_read($count);
    }    
    public function stream_tell(): int
    {
        return $this->stream->stream_tell();
    }    
    public function stream_eof(): bool
    {
        return $this->stream->stream_eof();
    }    
    public function stream_seek(int $offset, int $whence): int
    {
        return $this->stream->stream_seek($offset, $whence);
    }
    public function stream_write(string $data): int
    {
        return $this->stream->stream_write($data);
    }
}

