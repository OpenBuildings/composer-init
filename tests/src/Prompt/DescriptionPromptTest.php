<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\DescriptionPrompt;
use CL\ComposerInit\Prompt\GitConfig;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\DescriptionPrompt
 */
class DescriptionPromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     * @covers ::getGithub
     */
    public function testConstruct()
    {
        $GitConfig = new GitConfig();
        $github = new GithubMock();
        $prompt = new DescriptionPrompt($GitConfig, $github);

        $this->assertSame($GitConfig, $prompt->getGitConfig());
        $this->assertSame($github, $prompt->getGithub());
    }

    /**
     * @covers getDefault
     */
    public function testGetDefaultNull()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn(null);

        $github = new GithubMock();
        $prompt = new DescriptionPrompt($gitConfig, $github);

        $this->assertNull($prompt->getDefault());
        $this->assertEmpty($github->getHistory());
    }

    /**
     * @covers getDefault
     */
    public function testGetDefaultGithub()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn('octocat/Hello-World');

        $github = new GithubMock();
        $github->queueResponse('repo.json');

        $prompt = new DescriptionPrompt($gitConfig, $github);

        $this->assertEquals(
            'This your first repo!',
            $prompt->getDefault()
        );
        $request = $github->getHistory()->getLastRequest();
        $this->assertEquals(
            '/repos/octocat/Hello-World',
            $request->getUrl()
        );
    }

    /**
     * @covers getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\DescriptionPrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('TEST_DESCRIPTION');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Description</info> (TEST_DESCRIPTION): ',
                'TEST_DESCRIPTION'
            )
            ->willReturn('NEW_DESCRIPTION');

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['description' => 'NEW_DESCRIPTION'], $values);
    }
}
