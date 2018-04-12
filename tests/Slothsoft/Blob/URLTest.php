<?php
declare(strict_types = 1);
namespace Slothsoft\Blob;

use PHPUnit\Framework\TestCase;

class URLTest extends TestCase
{
    public function testCreateObjectUrl() {
        $resource = fopen('php://temp', 'w+');
        
        $url = URL::createObjectURL($resource);
        
        $this->assertRegExp('~^blob://\d+$~', $url);
    }
    
    public function testResolveObjectUrl() {
        $resource = fopen('php://temp', 'w+');
        
        $url = URL::createObjectURL($resource);
        
        $this->assertEquals($resource, URL::resolveObjectURL($url));
    }
    
    public function testRevokeObjectUrl() {
        $resource = fopen('php://temp', 'w+');
        
        $url = URL::createObjectURL($resource);
        
        URL::revokeObjectURL($url);
        
        $this->assertEquals(null, URL::resolveObjectURL($url));
    }
    
    public function testCreateTemporaryObject() {
        $content = 'hello world';
        
        $resource = URL::createTemporaryObject();
        
        $this->assertTrue(is_resource($resource));
        $this->assertEquals(strlen($content), fwrite($resource, $content));
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
    }
    
    public function testCreateTemporaryUrl() {
        $content = 'hello world';
        
        $url = URL::createTemporaryURL();
        
        $this->assertEquals(strlen($content), file_put_contents($url, $content));
        
        $this->assertEquals($content, file_get_contents($url));
        
        URL::revokeObjectURL($url);
        
        $this->assertEquals(null, URL::resolveObjectURL($url));
    }
}

