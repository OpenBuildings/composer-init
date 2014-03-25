<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\Copyright;

class CopyrightTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\Copyright::getName
     */
    public function testGetName()
    {
        $prompt = new Copyright();

        $this->assertEquals('copyright', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\Copyright::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getOrganization', 'getOwner'));

        $template
            ->expects($this->once())
            ->method('getOrganization')
            ->will($this->returnValue(array('name' => 'Organization Inc.')));

        $template
            ->expects($this->once())
            ->method('getOwner')
            ->will($this->returnValue(array('name' => 'Example User')));

        $prompt = new Copyright();

        $expected = array(
            date('Y').', Organization Inc.',
            date('Y').', Example User',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }

    /**
     * @covers CL\ComposerInit\Prompt\Copyright::getValuesForResponse
     */
    public function testGetValuesForResponse()
    {
        $prompt = new Copyright();

        $expected = array(
            'copyright' => '2014-2015, Test Company',
            'copyright_entity' => 'Test Company',
        );

        $this->assertEquals($expected, $prompt->getValuesForResponse('2014-2015, Test Company'));
    }
}
