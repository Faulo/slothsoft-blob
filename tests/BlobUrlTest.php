<?php
declare(strict_types = 1);

namespace Slothsoft\Blob;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class BlobUrlTest extends TestCase {
    
    /** @runInSeparateProcess */
    public function testCreateObjectUrl(): void {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        $this->assertIsResource($resource);
        
        $url = BlobUrl::createObjectURL($resource);
        
        $this->assertMatchesRegularExpression('~^blob://\d+$~', $url);
    }
    
    /** @runInSeparateProcess */
    public function testResolveObjectUrl(): void {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        $this->assertIsResource($resource);
        
        $url = BlobUrl::createObjectURL($resource);
        
        $this->assertEquals($resource, BlobUrl::resolveObjectURL($url));
    }
    
    /** @runInSeparateProcess */
    public function testRevokeObjectUrl(): void {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        $this->assertIsResource($resource);
        
        $url = BlobUrl::createObjectURL($resource);
        
        BlobUrl::revokeObjectURL($url);
        
        $this->assertEquals(null, BlobUrl::resolveObjectURL($url));
    }
    
    public function testCreateTemporaryObject(): void {
        $content = 'hello world';
        
        $resource = BlobUrl::createTemporaryObject();
        
        $this->assertTrue(is_resource($resource));
        $this->assertEquals(strlen($content), fwrite($resource, $content));
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
    }
    
    /** @runInSeparateProcess */
    public function testCreateTemporaryUrl(): void {
        $content = 'hello world';
        
        $url = BlobUrl::createTemporaryURL();
        
        $this->assertEquals(strlen($content), file_put_contents($url, $content));
        
        $this->assertEquals($content, file_get_contents($url));
        
        BlobUrl::revokeObjectURL($url);
        
        $this->assertEquals(null, BlobUrl::resolveObjectURL($url));
    }
}

