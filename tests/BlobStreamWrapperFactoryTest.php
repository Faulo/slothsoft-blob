<?php
declare(strict_types = 1);

namespace Slothsoft\Blob;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use DOMDocument;
use XSLTProcessor;

class BlobStreamWrapperFactoryTest extends TestCase {
    
    /** @return list<array{non-empty-string}> */
    public function contentProvider(): array {
        return [
            [
                'hello'
            ],
            [
                'world'
            ]
        ];
    }
    
    /** @return resource */
    private function createResource(string $content = '') {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        $this->assertIsResource($resource);
        if (strlen($content)) {
            fwrite($resource, $content);
        }
        return $resource;
    }
    
    /**
     *
     * @dataProvider contentProvider
     */
    public function testResource(string $content): void {
        $resource = $this->createResource($content);
        $length = strlen($content);
        $this->assertTrue($length > 0);
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, $length));
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, $length));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testUrlExists(string $content): void {
        $resource = $this->createResource($content);
        
        $url = BlobUrl::createObjectURL($resource);
        
        $this->assertTrue(file_exists($url));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testUrlGetContents(string $content): void {
        $resource = $this->createResource($content);
        
        $url = BlobUrl::createObjectURL($resource);
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testUrlPutContents(string $content): void {
        $resource = $this->createResource($content);
        $url = BlobUrl::createObjectURL($resource);
        
        file_put_contents($url, $content);
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testUrlAppendContents(string $content): void {
        $resource = $this->createResource($content);
        $url = BlobUrl::createObjectURL($resource);
        
        file_put_contents($url, '!!', FILE_APPEND);
        
        $this->assertEquals($content . '!!', file_get_contents($url));
        $this->assertEquals($content . '!!', file_get_contents($url));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testClosedUrlDoesNotExist(string $content): void {
        $resource = $this->createResource($content);
        $url = BlobUrl::createObjectURL($resource);
        
        BlobUrl::revokeObjectURL($url);
        clearstatcache();
        
        $this->assertFalse(file_exists($url));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testClosedUrlIsNotResource(string $content): void {
        $resource = $this->createResource($content);
        $url = BlobUrl::createObjectURL($resource);
        
        BlobUrl::revokeObjectURL($url);
        clearstatcache();
        
        $this->assertFalse(is_resource($resource));
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testLoadDocument(string $tag): void {
        $content = "<$tag/>";
        $resource = $this->createResource($content);
        $url = BlobUrl::createObjectURL($resource);
        
        $doc = new DOMDocument();
        $doc->load($url);
        
        $this->assertNotNull($doc->documentElement);
        $this->assertEquals($tag, $doc->documentElement->tagName);
    }
    
    /**
     *
     * @runInSeparateProcess
     * @dataProvider contentProvider
     */
    public function testSaveDocument(string $tag): void {
        $resource = $this->createResource();
        $url = BlobUrl::createObjectURL($resource);
        
        $doc = new DOMDocument();
        $doc->appendChild($doc->createElement($tag));
        $doc->save($url);
        
        $this->assertEquals($doc->saveXML(), file_get_contents($url));
    }
    
    /** @runInSeparateProcess */
    public function testTransformDocument(): void {
        $dataXml = <<<EOT
        <xml>
            hello world
        </xml>
        EOT;
        $dataResource = $this->createResource($dataXml);
        $dataUrl = BlobUrl::createObjectURL($dataResource);
        $dataDoc = new DOMDocument();
        $dataDoc->load($dataUrl);
        
        $templateXml = <<<EOT
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
            <xsl:template match="/">
                <transformed-xml>
                    <xsl:value-of select="normalize-space(.)"/>
                </transformed-xml>
            </xsl:template>
        </xsl:stylesheet>
        EOT;
        $templateResource = $this->createResource($templateXml);
        $templateUrl = BlobUrl::createObjectURL($templateResource);
        $templateDoc = new DOMDocument();
        $templateDoc->load($templateUrl);
        
        $resultResource = $this->createResource();
        $resultUrl = BlobUrl::createObjectURL($resultResource);
        
        $xslt = new XSLTProcessor();
        $xslt->importStylesheet($templateDoc);
        $xslt->transformToUri($dataDoc, $resultUrl);
        
        $resultDoc = new DOMDocument();
        $resultDoc->load($resultUrl);
        
        $this->assertNotNull($resultDoc->documentElement);
        $this->assertEquals('transformed-xml', $resultDoc->documentElement->tagName);
        $this->assertEquals('hello world', $resultDoc->documentElement->textContent);
    }
}

