<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\Description;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\Description
 */
class DescriptionTest extends AbstractTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $prompt = new Description();

        $this->assertEquals('description', $prompt->getName());
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $prompt = new Description();

        $this->assertEquals('Description', $prompt->getTitle());
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
            ->with($this->equalTo('description'))
            ->will($this->returnValue('description test'));

        $prompt = new Description();

        $expected = array(
            'description test',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }
}
