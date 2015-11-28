<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Test\ClientMock;
use Symfony\Component\Console\Output\NullOutput;
use CL\ComposerInit\Prompt\BugsPrompt;
use CL\ComposerInit\GitConfig;
use Closure;
use RuntimeException;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\BugsPrompt
 */
class BugsPromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     * @covers ::getGithub
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $github = new ClientMock();
        $prompt = new BugsPrompt($gitConfig, $github);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
        $this->assertSame($github, $prompt->getGithub());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefaultNull()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn(null);

        $github = new ClientMock();
        $prompt = new BugsPrompt($gitConfig, $github);

        $this->assertNull($prompt->getDefault());
        $this->assertEmpty($github->getHistory());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefaultGithub()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn('octocat/Hello-World');

        $github = new ClientMock();
        $github->queueResponse('github/repo.json');

        $prompt = new BugsPrompt($gitConfig, $github);

        $this->assertEquals(
            'https://github.com/octocat/Hello-World/issues/new',
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
            ->getMockBuilder('CL\ComposerInit\Prompt\BugsPrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('https://example.com/issues');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('askAndValidate')
            ->with(
                $this->identicalTo($output),
                '<info>Issues url</info> (https://example.com/issues): ',
                $this->callback(function(Closure $test) {
                    $this->assertEquals(
                        'https://example.com/issues',
                        $test('https://example.com/issues')
                    );

                    try {
                        $test('asd');
                    } catch (RuntimeException $e) {
                        return true;
                    }

                    return false;
                }),
                false,
                'https://example.com/issues'
            )
            ->willReturn('https://example.com/issues/new');

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['bugs' => 'https://example.com/issues/new'], $values);
    }
}
