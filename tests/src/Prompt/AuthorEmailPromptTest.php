<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\AuthorEmailPrompt;
use CL\ComposerInit\Prompt\GitConfig;
use Symfony\Component\Console\Output\NullOutput;
use Closure;
use RuntimeException;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\AuthorEmailPrompt
 */
class AuthorEmailPromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $prompt = new AuthorEmailPrompt($gitConfig);

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
            ->with($this->equalTo('user.email'))
            ->willReturn('TEST_RETURN');

        $prompt = new AuthorEmailPrompt($gitConfig);

        $this->assertEquals('TEST_RETURN', $prompt->getDefault());
    }

    /**
     * @covers getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\AuthorEmailPrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('default@example.com');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('askAndValidate')
            ->with(
                $this->identicalTo($output),
                '<info>Author email</info> (default@example.com): ',
                $this->callback(function(Closure $test) {
                    $this->assertEquals('valid@example.com', $test('valid@example.com'));
                    try {
                        $test('asd');
                    } catch (RuntimeException $e) {
                        return true;
                    }

                    return false;
                }),
                false,
                'default@example.com'
            )
            ->willReturn('result@example.com');

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['author_email' => 'result@example.com'], $values);
    }
}
