<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\AuthorName;

class AuthorNameTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\AuthorName::getName
     */
    public function testGetName()
    {
        $prompt = new AuthorName();

        $this->assertEquals('author_name', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\AuthorName::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getGitConfig'));
        $template
            ->expects($this->once())
            ->method('getGitConfig')
            ->with($this->equalTo('user.name'))
            ->will($this->returnValue('Example User'));

        $prompt = new AuthorName();

        $expected = array(
            'Example User',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }
}
