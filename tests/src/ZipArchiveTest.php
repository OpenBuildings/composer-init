<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class ZipArchiveTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->zip = new ZipArchive();
        $this->zip->open($this->getTestsDir().'test.zip');
    }

    public function tearDown()
    {
        $this->zip->close();

        parent::tearDown();
    }

    /**
     * @covers CL\ComposerInit\ZipArchive::getRootDir
     */
    public function testGetRootDir()
    {
        $this->assertEquals('clippings-package-template-971a36f/', $this->zip->getRootDir());
    }

    /**
     * @covers CL\ComposerInit\ZipArchive::includeFile
     */
    public function testIncludeFile()
    {
        $this->assertFalse(function_exists('test_include_function'));

        $this->zip->includeFile('clippings-package-template-971a36f/test_include.php');

        $this->assertTrue(function_exists('test_include_function'));
    }

    /**
     * @covers CL\ComposerInit\ZipArchive::extractDirTo
     */
    public function testExtractDirTo()
    {
        $this->zip->extractDirTo('clippings-package-template-971a36f/root', $this->getTestsDir().'test_extract');

        $files = array(
            array('test_extract'),
            array('test_extract', 'clippings-package-template-971a36f'),
            array('test_extract', 'clippings-package-template-971a36f', 'root'),
            array('test_extract', 'clippings-package-template-971a36f', 'root', '.gitignore'),
            array('test_extract', 'clippings-package-template-971a36f', 'root', 'README.md'),
            array('test_extract', 'clippings-package-template-971a36f', 'root', 'src'),
            array('test_extract', 'clippings-package-template-971a36f', 'root', 'src', 'Init.php'),
        );

        foreach ($files as $file)
        {
            $this->assertFileExists($this->getTestsDir().implode(DIRECTORY_SEPARATOR, $file));
        }

        $filesIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->getTestsDir().'test_extract', FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($filesIterator as $path) {
            if (is_dir($path->getPathname())) {
                rmdir($path->getPathname());
            } else {
                unlink($path->getPathname());
            }
        }

        rmdir($this->getTestsDir().'test_extract');
    }
}
