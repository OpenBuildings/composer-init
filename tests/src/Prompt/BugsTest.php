<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\Bugs;

class BugsTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\Bugs::getName
     */
    public function testGetName()
    {
        $prompt = new Bugs();

        $this->assertEquals('bugs', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\Bugs::getDefaults
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
