<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Output\NullOutput;
use CL\ComposerInit\Prompt\PackageNamePrompt;
use CL\ComposerInit\Prompt\GitConfig;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\PackageNamePrompt
 */
class PackageNamePromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getGitConfig
     */
    public function testConstruct()
    {
        $gitConfig = new GitConfig();
        $prompt = new PackageNamePrompt($gitConfig);

        $this->assertSame($gitConfig, $prompt->getGitConfig());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefault()
    {
        $getConfig = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\GitConfig')
            ->getMock();

        $getConfig
            ->method('getOrigin')
            ->willReturn('TEST');

        $prompt = new PackageNamePrompt($getConfig);

        $this->assertEquals('TEST', $prompt->getDefault());
    }

    /**
     * @covers ::getValues
     */
    public function testGetValues()
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\PackageNamePrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $prompt
            ->method('getDefault')
            ->willReturn('PACKAGE_NAME');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Package Name</info> (PACKAGE_NAME): ',
                'PACKAGE_NAME'
            )
            ->willReturn('NEW_PACKAGE');

        $values = $prompt->getValues($output, $dialog);

        $expected = [
            'package_name' => 'NEW_PACKAGE',
        ];

        $this->assertEquals($expected, $values);
    }
}
