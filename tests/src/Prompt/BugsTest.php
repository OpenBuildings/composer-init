<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\Bugs;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\Bugs
 */
class BugsTest extends AbstractTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $prompt = new Bugs();

        $this->assertEquals('bugs', $prompt->getName());
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $prompt = new Bugs();

        $this->assertEquals('Issues url', $prompt->getTitle());
    }

    /**
     * @covers ::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField'));
        $template
            ->expects($this->once())
            ->method('getRepoField')
            ->with($this->equalTo('html_url'))
            ->will($this->returnValue('http://example.com'));

        $prompt = new Bugs();

        $expected = array(
            'http://example.com/issues/new',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }
}
