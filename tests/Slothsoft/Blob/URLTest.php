<?php
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
}

