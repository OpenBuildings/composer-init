<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\AuthorNamePrompt;
use CL\ComposerInit\Prompt\GitConfig;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\AuthorNamePrompt
 */
class AuthorNamePromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $prompt = new AuthorNamePrompt($gitConfig);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
    }

    /**
     * @covers getDefault
     */
    public function testGetDefault()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $gitConfig
            ->method('get')
            ->with($this->equalTo('user.name'))
            ->willReturn('TEST_RETURN');

        $prompt = new AuthorNamePrompt($gitConfig);

        $this->assertEquals('TEST_RETURN', $prompt->getDefault());
    }

    /**
     * @covers getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\AuthorNamePrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('TEST_NAME');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Author name</info> (TEST_NAME): ',
                'TEST_NAME'
            )
            ->willReturn('NEW_NAME');

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['author_name' => 'NEW_NAME'], $values);
    }
}
