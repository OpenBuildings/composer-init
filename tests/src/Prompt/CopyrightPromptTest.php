<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Test\ClientMock;
use Symfony\Component\Console\Output\NullOutput;
use CL\ComposerInit\Prompt\CopyrightPrompt;
use CL\ComposerInit\GitConfig;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\CopyrightPrompt
 */
class CopyrightPromptTest extends PHPUnit_Framework_TestCase
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
        $prompt = new CopyrightPrompt($gitConfig, $github);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
        $this->assertSame($github, $prompt->getGithub());
    }

    /**
     * @covers ::getDefaults
     */
    public function testGetDefaultsMinimal()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn(null);

        $gitConfig
            ->method('get')
            ->with($this->equalTo('user.name'))
            ->willReturn(null);

        $github = new ClientMock();

        $prompt = new CopyrightPrompt($gitConfig, $github);

        $expected = [get_current_user()];

        $this->assertEquals($expected, $prompt->getDefaults());
        $this->assertEmpty($github->getHistory());
    }

    /**
     * @covers ::getDefaults
     */
    public function testGetDefaultsGitConfig()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn(null);

        $gitConfig
            ->method('get')
            ->with($this->equalTo('user.name'))
            ->willReturn('TEST_USER');

        $github = new ClientMock();

        $prompt = new CopyrightPrompt($gitConfig, $github);

        $expected = ['TEST_USER', get_current_user()];

        $this->assertEquals($expected, $prompt->getDefaults());
        $this->assertEmpty($github->getHistory());
    }

    /**
     * @covers ::getDefaults
     */
    public function testGetDefaultsFull()
    {
        $gitConfig = $this
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $gitConfig
            ->method('getOrigin')
            ->willReturn('octocat/Hello-World');

        $gitConfig
            ->method('get')
            ->with($this->equalTo('user.name'))
            ->willReturn('TEST_USER');

        $github = new ClientMock();
        $github
            ->queueResponse('github/repo.json')
            ->queueResponse('github/organization.json')
            ->queueResponse('github/user.json');

        $prompt = new CopyrightPrompt($gitConfig, $github);

        $expected = ['github', 'monalisa octocat', 'TEST_USER', get_current_user()];

        $this->assertEquals($expected, $prompt->getDefaults());

        $history = $github->getHistory();
        $this->assertEquals('/repos/octocat/Hello-World', (string) $history[0]['request']->getUri());
        $this->assertEquals('/orgs/github', (string) $history[1]['request']->getUri());
        $this->assertEquals('/users/octocat', (string) $history[2]['request']->getUri());
    }

    /**
     * @covers ::prependToArray
     */
    public function testPrependToArray()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\CopyrightPrompt')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $result = $prompt->prependToArray('TEST', ['test', 'test2']);

        $this->assertEquals(['TESTtest', 'TESTtest2'], $result);
    }

    /**
     * @covers ::getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\CopyrightPrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefaults'])
            ->getMock();

        $prompt
            ->method('getDefaults')
            ->willReturn(['github', 'TEST_USER']);

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $year = date('Y');

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                "<info>Copyright</info> ({$year}, github): ",
                "{$year}, github",
                ["{$year}, github", "{$year}, TEST_USER"]
            )
            ->willReturn('2012, NEW_NAME');

        $values = $prompt->getValues($output, $dialog);
        $expected = [
            'copyright' => '2012, NEW_NAME',
            'copyright_entity' => 'NEW_NAME',
        ];
        $this->assertEquals($expected, $values);
    }
}
