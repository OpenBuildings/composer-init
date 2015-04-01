<?php

namespace CL\ComposerInit\Prompt\Test;

use CL\ComposerInit\Prompt\GitConfig;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\GitConfig
 */
class GitConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::shell
     */
    public function testShell()
    {
        $gitConfig = new GitConfig();
        $this->assertEquals("VALUE", $gitConfig->shell('echo VALUE'));
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->setMethods(['shell'])
            ->getMock();

        $gitConfig
            ->method('shell')
            ->with('git option CONFIG_NAME')
            ->willReturn('VALUE');

        $this->assertEquals('VALUE', $gitConfig->get('CONFIG_NAME'));
    }

    public function dataGetOrigin()
    {
        return [
            ['https://github.com/clippings/composer-init.git', 'clippings/composer-init'],
            ['git@github.com:clippings/composer-init.git', 'clippings/composer-init'],
        ];
    }

    /**
     * @dataProvider dataGetOrigin
     * @covers ::getOrigin
     */
    public function testGetOrigin($url, $value)
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->setMethods(['get'])
            ->getMock();

        $gitConfig
            ->method('get')
            ->with('remote.origin.url')
            ->will($this->onConsecutiveCalls(null, $url));

        $this->assertEquals(null, $gitConfig->getOrigin());
        $this->assertEquals($value, $gitConfig->getOrigin());
    }
}
