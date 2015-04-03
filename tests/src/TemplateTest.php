<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Template;

/**
 * @coversDefaultClass CL\ComposerInit\Template
 */
class TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGithub
     */
    public function testConstruct()
    {
        $client = new ClientMock();
        $template = new Template($client);

        $this->assertSame($client, $template->getGithub());
    }

    /**
     * @covers ::open
     * @covers ::getZip
     * @covers ::getRoot
     * @covers ::getPromptNames
     */
    public function testOpen()
    {
        $client = new ClientMock();
        $client->queueResponse('test.zip');

        $template = new Template($client);

        $template->open('TEST_URL');

        $this->assertEquals(
            'TEST_URL',
            $client->getHistory()->getLastRequest()->getUrl()
        );

        $zip = $template->getZip();
        $this->assertInstanceOf('ZipArchive', $zip);
        $this->assertEquals(
            'clippings-package-template-971a36f/',
            $template->getRoot()
        );

        $this->assertEquals(
            ['title', 'description'],
            $template->getPromptNames()
        );
    }

    /**
     * @covers ::populateValues
     */
    public function testPopulateValues()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $template->setValues([
            'my val' => 'V1',
            'my other val' => 'V2',
        ]);

        $content = 'some content {% my val %} {% my val %} {% my other val %}';

        $this->assertEquals(
            'some content V1 V1 V2',
            $template->populateValues($content)
        );
    }

    /**
     * @covers ::setValues
     * @covers ::getValues
     */
    public function testValues()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEmpty($template->getValues());

        $template->setValues(['a', 'b']);

        $this->assertEquals(['a', 'b'], $template->getValues());
    }

    /**
     * @covers ::putInto
     */
    public function testPutInto()
    {
        $client = new ClientMock();
        $client->queueResponse('test.zip');

        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->setConstructorArgs([$client])
            ->setMethods(['putFile', 'putDir'])
            ->getMock();

        $template->open('TEST');

        $template->setValues([
            'author_name' => 'AUTHOR',
            'title' => 'TITLE'
        ]);

        $template
            ->expects($this->at(0))
            ->method('putFile')
            ->with('temp/.DS_Store');

        $template
            ->expects($this->at(1))
            ->method('putFile')
            ->with('temp/.gitignore', "/vendor/\n/build/\n");

        $template
            ->expects($this->at(2))
            ->method('putFile')
            ->with('temp/README.md', "# Test Project Named TITLE\n");

        $template
            ->expects($this->at(3))
            ->method('putDir')
            ->with('temp/src/');

        $template
            ->expects($this->at(4))
            ->method('putFile')
            ->with(
                'temp/src/Init.php',
                "<?php\n\n/**\n * @author    AUTHOR\n */\nclass Init {}\n"
            );

        $template->putInto('temp');
    }

    /**
     * @covers ::putFile
     */
    public function testPutFile()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $filename = __DIR__.'/../test.txt';

        $template->putFile($filename, 'content');

        $this->assertEquals('content', file_get_contents($filename));

        $template->putFile($filename, 'content2');

        $this->assertEquals('content2', file_get_contents($filename));

        unlink($filename);
    }

    /**
     * @covers ::putDir
     */
    public function testPutDir()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $dirname = __DIR__.'/../test_dir';

        $template->putDir($dirname);

        $this->assertTrue(is_dir($dirname));

        $template->putDir($dirname);

        $this->assertTrue(is_dir($dirname));

        rmdir($dirname);
    }
}
