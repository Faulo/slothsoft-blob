<?php
namespace Slothsoft\Blob;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;
use DOMDocument;
use XSLTProcessor;

class StreamWrapperTest extends TestCase
{
    public function testReadStream() {
        $content = 'hello world';
        
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, $content);
        
        $url = URL::createObjectURL($resource);
        $this->assertTrue(file_exists($url));
        
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
        $this->assertTrue(file_exists($url));
        
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
        $this->assertTrue(file_exists($url));
        
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
        $this->assertTrue(file_exists($url));
        
        $this->assertEquals($content, file_get_contents($url));
        
        $this->assertTrue(is_resource($resource));
        
        URL::revokeObjectURL($url);
        clearstatcache();
        
        $this->assertFalse(is_resource($resource));
        $this->assertFalse(file_exists($url));
        
        $this->expectException(Warning::class);
        file_get_contents($url);
    }
    
    public function testLoadDocument() {
        $content = '<xml/>';
        
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, $content);
        $url = URL::createObjectURL($resource);
        
        $doc = new DOMDocument();
        $doc->load($url);
        
        $this->assertEquals('xml', $doc->documentElement->tagName);
    }
    
    public function testSaveDocument() {
        $resource = fopen('php://temp', 'w+');
        $url = URL::createObjectURL($resource);
        
        $doc = new DOMDocument();
        $doc->appendChild($doc->createElement('xml'));
        
        $content = $doc->saveXML();
        $doc->save($url);
        
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        fseek($resource, 0);
        $this->assertEquals($content, fread($resource, strlen($content)));
        
        $this->assertEquals($content, file_get_contents($url));
        $this->assertEquals($content, file_get_contents($url));
    }
    
    public function testTransformDocument() {
        $dataXml = <<<EOT
<xml>
    hello world
</xml>
EOT;
        $dataResource = fopen('php://temp', 'w+');
        fwrite($dataResource, $dataXml);
        $dataUrl = URL::createObjectURL($dataResource);
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
        $templateResource = fopen('php://temp', 'w+');
        fwrite($templateResource, $templateXml);
        $templateUrl = URL::createObjectURL($templateResource);
        $templateDoc = new DOMDocument();
        $templateDoc->load($templateUrl);
        
        $resultResource = fopen('php://temp', 'w+');
        $resultUrl = URL::createObjectURL($resultResource);
        
        $xslt = new XSLTProcessor();
        $xslt->importStylesheet($templateDoc);
        $xslt->transformToUri($dataDoc, $resultUrl);
        
        $resultDoc = new DOMDocument();
        $resultDoc->load($resultUrl);
        
        $this->assertEquals('transformed-xml', $resultDoc->documentElement->tagName);
        $this->assertEquals('hello world', $resultDoc->documentElement->textContent);
    }
}

