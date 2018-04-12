<?php
namespace Slothsoft\Blob\StreamWrapper;

interface StreamWrapperInterface
{
    /**
     * @return array
     * @see http://php.net/manual/de/function.fstat.php
     */
    public function stream_stat() : array;
    
    /**
     * @param int $count
     * @return string
     * @see http://php.net/manual/de/function.fread.php
     */
    public function stream_read(int $count): string;
    
    /**
     * @return int
     * @see http://php.net/manual/de/function.ftell.php
     */
    public function stream_tell(): int;
    
    /**
     * @return bool
     * @see http://php.net/manual/de/function.feof.php
     */
    public function stream_eof(): bool;
    
    /**
     * @param int $offset
     * @param int $whence
     * @return int
     * @see @see http://php.net/manual/de/function.fseek.php
     */
    public function stream_seek(int $offset, int $whence): int;
    
    /**
     * @param string $data
     * @return int
     * @see http://php.net/manual/de/function.fwrite.php
     */
    public function stream_write(string $data): int;
}

