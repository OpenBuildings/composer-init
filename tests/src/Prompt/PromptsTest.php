<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\Prompts;
use CL\ComposerInit\Inflector;
use CL\ComposerInit\GitConfig;
use GuzzleHttp\Client;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\Prompts
 */
class PromptsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGithub
     * @covers ::getGitConfig
     * @covers ::getInflector
     * @covers ::get
     * @covers ::add
     */
    public function testConstruct()
    {
        $github = new Client();
        $gitConfig = new GitConfig();
        $inflector = new Inflector();

        $prompts = new Prompts($gitConfig, $github, $inflector);

        $this->assertSame($github, $prompts->getGithub());
        $this->assertSame($gitConfig, $prompts->getGitConfig());
        $this->assertSame($inflector, $prompts->getInflector());

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\AuthorEmailPrompt',
            $prompts->get('author_email')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\AuthorNamePrompt',
            $prompts->get('author_name')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\BugsPrompt',
            $prompts->get('bugs')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\CopyrightPrompt',
            $prompts->get('copyright')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\DescriptionPrompt',
            $prompts->get('description')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\PhpNamespacePrompt',
            $prompts->get('php_namespace')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\PackageNamePrompt',
            $prompts->get('package_name')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\SlackNotificationPrompt',
            $prompts->get('slack_notification')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\Prompt\TitlePrompt',
            $prompts->get('title')
        );
    }

    /**
     * @covers ::getValues
     */
    public function testGetValues()
    {
        $output = new NullOutput();
        $dialog = new DialogHelper();

        $prompt1 = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\PromptInterface')
            ->getMock();

        $prompt1
            ->method('getValues')
            ->with(
                $this->identicalTo($output),
                $this->identicalTo($dialog)
            )
            ->willReturn(['val1' => 'VAL1']);

        $prompt2 = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\PromptInterface')
            ->getMock();

        $prompt2
            ->method('getValues')
            ->with(
                $this->identicalTo($output),
                $this->identicalTo($dialog)
            )
            ->willReturn(['val2' => 'VAL2']);

        $prompts = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\Prompts')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $prompts
            ->method('get')
            ->will($this->returnValueMap([
                ['prompt1', $prompt1],
                ['prompt2', $prompt2],
            ]));

        $values = $prompts->getValues(['prompt1', 'prompt2'], $output, $dialog);

        $expected = [
            'val1' => 'VAL1',
            'val2' => 'VAL2'
        ];

        $this->assertEquals($expected, $values);
    }
}
