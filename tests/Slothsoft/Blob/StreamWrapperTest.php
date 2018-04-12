<?php
namespace Slothsoft\Blob;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

class StreamWrapperTest extends TestCase
{
    public function testReadStream() {
        $content = 'hello world';
        
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, $content);
        
        $url = URL::createObjectURL($resource);
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    public function testWriteStream() {
        $content = 'hello world';
        
        $resource = fopen('php://temp', 'w+');
        
        $url = URL::createObjectURL($resource);
        
        file_put_contents($url, $content);
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    public function testAppendStream() {
        $content = 'hello world';
        
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, 'hello');
        
        $url = URL::createObjectURL($resource);
        
        file_put_contents($url, ' world', FILE_APPEND);
        
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    public function testCloseStream() {
        $content = 'hello world';
        
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, $content);
        
        $url = URL::createObjectURL($resource);
        
        $this->assertEquals($content, file_get_contents($url));
        
        $this->assertTrue(is_resource($resource));
        $this->assertTrue(file_exists($url));
        
        URL::revokeObjectURL($url);
        clearstatcache();
        
        $this->assertFalse(is_resource($resource));
        $this->assertFalse(file_exists($url));
        
        $this->expectException(Warning::class);
        file_get_contents($url);
    }
}

