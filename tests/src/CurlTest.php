<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\Curl;

class CurlTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Curl::get
     * @covers CL\ComposerInit\Curl::execute
     * @covers CL\ComposerInit\Curl::getError
     */
    public function testGet()
    {
        $content = Curl::get('file://'.$this->getTestsDir().'.composer-init.json');

        $this->assertEquals(file_get_contents($this->getTestsDir().'.composer-init.json'), $content);
    }

    /**
     * @covers CL\ComposerInit\Curl::getJSON
     */
    public function testGetJSON()
    {
        $content = Curl::getJSON('file://'.$this->getTestsDir().'.composer-init.json');

        $this->assertEquals(array('token' => '123456789012345678901234567890'), $content);

        $this->setExpectedException('Exception', 'Unable to parse response body into JSON: 4');

        Curl::getJSON('file://'.$this->getTestsDir().'test.txt');
    }

    /**
     * @covers CL\ComposerInit\Curl::getError
     * @covers CL\ComposerInit\Curl::execute
     */
    public function testGetError()
    {
        $this->setExpectedException('Exception', 'Request for file://');

        Curl::getJSON('file://'.$this->getTestsDir().'missing-file');
    }

    /**
     * @covers CL\ComposerInit\Curl::getError
     */
    public function testGetError404()
    {
        $this->setExpectedException('Exception', 'The server returned error code 404');

        Curl::getJSON('http://example.com/missing-file');
    }

    /**
     * @covers CL\ComposerInit\Curl::download
     */
    public function testDownload()
    {
        $content = Curl::download('file://'.$this->getTestsDir().'.composer-init.json', $this->getTestsDir().'curl_downloaded_test.txt');

        $this->assertFileEquals($this->getTestsDir().'.composer-init.json', $this->getTestsDir().'curl_downloaded_test.txt');

        unlink($this->getTestsDir().'curl_downloaded_test.txt');
    }
}
