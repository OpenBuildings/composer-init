<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\AuthorEmail;

class AuthorEmailTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\AuthorEmail::getName
     */
    public function testGetName()
    {
        $prompt = new AuthorEmail();

        $this->assertEquals('author_email', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\AuthorEmail::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getGitConfig'));
        $template
            ->expects($this->once())
            ->method('getGitConfig')
            ->with($this->equalTo('user.email'))
            ->will($this->returnValue('test@example.com'));

        $prompt = new AuthorEmail();

        $expected = array(
            'test@example.com',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }
}
