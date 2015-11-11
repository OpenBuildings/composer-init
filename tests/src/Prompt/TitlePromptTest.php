<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Output\NullOutput;
use CL\ComposerInit\Test\ClientMock;
use CL\ComposerInit\Prompt\TitlePrompt;
use CL\ComposerInit\GitConfig;
use CL\ComposerInit\Inflector;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\TitlePrompt
 */
class TitlePromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     * @covers ::getGithub
     * @covers ::getInflector
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $github = new ClientMock();
        $inflector = new Inflector();
        $prompt = new TitlePrompt($gitConfig, $github, $inflector);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
        $this->assertSame($github, $prompt->getGithub());
        $this->assertSame($inflector, $prompt->getInflector());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefaultNull()
    {
        $getConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $getConfig
            ->method('getOrigin')
            ->willReturn(null);

        $inflector = $this
            ->getMockBuilder('CL\ComposerInit\Inflector')
            ->getMock();

        $inflector
            ->method('title')
            ->with(getcwd())
            ->willReturn('INFLECTED');

        $github = new ClientMock();
        $prompt = new TitlePrompt($getConfig, $github, $inflector);

        $this->assertEquals('INFLECTED', $prompt->getDefault());
        $this->assertEmpty($github->getHistory());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefaultGithub()
    {
        $getConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $getConfig
            ->method('getOrigin')
            ->willReturn('octocat/Hello-World');

        $github = new ClientMock();
        $github->queueResponse('github/repo.json');

        $inflector = new Inflector();

        $prompt = new TitlePrompt($getConfig, $github, $inflector);

        $this->assertEquals(
            'Hello World',
            $prompt->getDefault()
        );
        $history = $github->getHistory();
        $this->assertEquals(
            '/repos/octocat/Hello-World',
            (string) $history[0]['request']->getUri()
        );
    }

    /**
     * @covers ::getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\TitlePrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('TITLE');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Title</info> (TITLE): ',
                'TITLE'
            )
            ->willReturn('NEW_TITLE');

        $values = $prompt->getValues($output, $dialog);
        $expected = [
            'title' => 'NEW_TITLE',
            'title_underline' => '=========',
        ];

        $this->assertEquals($expected, $values);
    }
}
