<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\AuthorEmail;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\AuthorEmail
 */
class AuthorEmailTest extends AbstractTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $prompt = new AuthorEmail();

        $this->assertEquals('author_email', $prompt->getName());
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $prompt = new AuthorEmail();

        $this->assertEquals('Author Email', $prompt->getTitle());
    }

    /**
     * @covers ::getDefaults
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
