<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\Title;

class TitleTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\Title::getName
     */
    public function testGetName()
    {
        $prompt = new Title();

        $this->assertEquals('title', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\Title::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField'));
        $template
            ->expects($this->once())
            ->method('getRepoField')
            ->with($this->equalTo('name'))
            ->will($this->returnValue('test name'));

        $prompt = new Title();

        $expected = array(
            'Test Name',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }

    /**
     * @covers CL\ComposerInit\Prompt\Title::getValuesForResponse
     */
    public function testGetValuesForResponse()
    {
        $prompt = new Title();

        $expected = array(
            'title' => 'test response',
            'title_underline' => '=============',
        );

        $this->assertEquals($expected, $prompt->getValuesForResponse('test response'));
    }
}
