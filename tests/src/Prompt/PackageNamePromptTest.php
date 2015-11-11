<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Output\NullOutput;
use CL\ComposerInit\Prompt\PackageNamePrompt;
use CL\ComposerInit\GitConfig;

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
            ->getMockBuilder('CL\ComposerInit\GitConfig')
            ->getMock();

        $getConfig
            ->method('getOrigin')
            ->willReturn('TEST');

        $prompt = new PackageNamePrompt($getConfig);

        $this->assertEquals('TEST', $prompt->getDefault());
    }

    public function dataToCamelCase()
    {
        return [
            ['test1', 'Test1'],
            ['test-1', 'Test1'],
            ['clippings-layout', 'ClippingsLayout'],
            ['clippings_layout', 'ClippingsLayout'],
            ['to_be_great', 'ToBeGreat'],
        ];
    }

    /**
     * @dataProvider dataToCamelCase
     * @covers ::toCamelCase
     */
    public function testToCamelCase($text, $expected)
    {
        $prompt = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\PackageNamePrompt')
            ->disableOriginalConstructor()
            ->setMethods(['getDefault'])
            ->getMock();

        $this->assertEquals($expected, $prompt->toCamelCase($text));
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
            ->willReturn('clippings/composer-init');

        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Package Name</info> (clippings/composer-init): ',
                'clippings/composer-init'
            )
            ->willReturn('clippings/composer-init');

        $values = $prompt->getValues($output, $dialog);

        $expected = [
            'package_name' => 'clippings/composer-init',
            'package_owner' => 'clippings',
            'package_title' => 'composer-init',
            'package_classname' => 'ComposerInit',
        ];

        $this->assertEquals($expected, $values);
    }
}
