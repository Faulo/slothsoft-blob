<?php
declare(strict_types = 1);
namespace Slothsoft\Blob\StreamWrapper;


class ResourceStreamWrapper implements StreamWrapperInterface
{
    private $handle;

    public function __construct($resource)
    {
        $this->handle = $resource;
    }
    public function stream_stat(): array
    {
        return fstat($this->handle);
    }
    public function stream_read(int $count): string
    {
        return fread($this->handle, $count);
    }
    public function stream_tell(): int
    {
        return ftell($this->handle);
    }
    public function stream_eof(): bool
    {
        return feof($this->handle);
    }
    public function stream_seek(int $offset, int $whence = SEEK_SET): int
    {
        return fseek($this->handle, $offset, $whence);
    }
    public function stream_write(string $data): int
    {
        return fwrite($this->handle, $data);
    }
}