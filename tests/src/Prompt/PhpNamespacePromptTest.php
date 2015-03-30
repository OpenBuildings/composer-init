<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\PhpNamespacePrompt;
use CL\ComposerInit\Prompt\GitConfig;
use CL\ComposerInit\Prompt\Inflector;
use Symfony\Component\Console\Output\NullOutput;
use Closure;
use RuntimeException;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\PhpNamespacePrompt
 */
class PhpNamespacePromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $inflector = new Inflector();
        $prompt = new PhpNamespacePrompt($gitConfig, $inflector);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
        $this->assertSame($inflector, $prompt->getInflector());
    }

    /**
     * @covers getDefaults
     */
    public function testGetDefaultsNull()
    {
        $configMock = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $configMock
            ->method('getOrigin')
            ->willReturn(null);

        $inflector = new Inflector();
        $prompt = new PhpNamespacePrompt($configMock, $inflector);

        $this->assertEmpty($prompt->getDefaults());
    }

    /**
     * @covers getDefaults
     */
    public function testGetDefaultsGithub()
    {
        $configMock = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $configMock
            ->method('getOrigin')
            ->willReturn('octocat/Hello-World');

        $inflector = new Inflector();
        $prompt = new PhpNamespacePrompt($configMock, $inflector);

        $expected = [
            'Octocat\\HelloWorld',
            'OC\\HelloWorld',
        ];

        $this->assertEquals(
            $expected,
            $prompt->getDefaults()
        );
    }

    /**
     * @covers getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\PhpNamespacePrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefaults'])
            ->getMock();

        $prompt
            ->method('getDefaults')
            ->willReturn(['Default\\Test', 'D\\Test']);

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('askAndValidate')
            ->with(
                $this->identicalTo($output),
                '<info>PHP Namespace</info> (Default\\Test): ',
                $this->callback(function(Closure $test) {
                    $this->assertEquals(
                        'Default\\Test',
                        $test('Default\\Test')
                    );

                    try {
                        $test('\Bad Namespace');
                    } catch (RuntimeException $e) {
                        return true;
                    }

                    return false;
                }),
                false,
                'Default\\Test',
                ['Default\\Test', 'D\\Test']
            )
            ->willReturn('New\\Test');

        $values = $prompt->getValues($output, $dialog);

        $expected = [
            'php_namespace' => 'New\\Test',
            'php_namespace_escaped' => 'New\\\\Test',
        ];

        $this->assertEquals($expected, $values);
    }
}
