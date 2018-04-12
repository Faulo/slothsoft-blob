<?php
namespace Slothsoft\Blob;

class StreamWrapper
{
    private $handle;
    
    public function stream_open(string $path, string $mode, int $options, &$opened_path)
    {
        $this->handle = URL::resolveObjectURL($path);
        if ($this->handle === null) {
            return false;
        }
        switch ($mode[0]) {
            case 'r':
            case 'w':
            case 'x':
            case 'c':
                fseek($this->handle, 0, SEEK_SET);
                break;
            case 'a':
                fseek($this->handle, 0, SEEK_END);
                break;
            default:
                return false;
        }
        return true;
    }
    
    public function stream_stat()
    {
        return fstat($this->handle);
    }
    
    public function url_stat(string $path) {
        if ($resource = URL::resolveObjectURL($path)) {
            return fstat($resource);
        } else {
            return false;
        }
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

