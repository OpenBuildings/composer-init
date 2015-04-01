<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\Prompts;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\Prompts
 */
class PromptsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getContainer
     */
    public function testConstruct()
    {
        $prompts = new Prompts();
        $this->assertInstanceOf('Pimple\Container', $prompts->getContainer());

        $contants = [
            'github'                    => 'GuzzleHttp\Client',
            'git_config'                => 'CL\ComposerInit\Prompt\GitConfig',
            'inflector'                 => 'CL\ComposerInit\Prompt\Inflector',
            'prompt.author_email'       => 'CL\ComposerInit\Prompt\AuthorEmailPrompt',
            'prompt.author_name'        => 'CL\ComposerInit\Prompt\AuthorNamePrompt',
            'prompt.bugs'               => 'CL\ComposerInit\Prompt\BugsPrompt',
            'prompt.copyright'          => 'CL\ComposerInit\Prompt\CopyrightPrompt',
            'prompt.description'        => 'CL\ComposerInit\Prompt\DescriptionPrompt',
            'prompt.php_namespace'      => 'CL\ComposerInit\Prompt\PhpNamespacePrompt',
            'prompt.slack_notification' => 'CL\ComposerInit\Prompt\SlackNotificationPrompt',
            'prompt.title'              => 'CL\ComposerInit\Prompt\TitlePrompt',
        ];

        foreach ($contants as $key => $class) {
            $this->assertInstanceOf(
                $class,
                $prompts->getContainer()->offsetGet($key)
            );
        }

        $this->assertEquals(array_keys($contants), $prompts->getContainer()->keys());
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $prompts = new Prompts();

        $this->assertSame(
            $prompts->get('author_email'),
            $prompts->getContainer()->offsetGet('prompt.author_email')
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
