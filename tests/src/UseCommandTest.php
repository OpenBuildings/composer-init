<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use CL\ComposerInit\UseCommand;
use Symfony\Component\Console\Application;

/**
 * @coversDefaultClass CL\ComposerInit\UseCommand
 */
class UseCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getPackegist
     * @covers ::getPrompts
     * @covers ::getTemplate
     * @covers ::configure
     */
    public function testConstruct()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $prompts = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\Prompts')
            ->disableOriginalConstructor()
            ->getMock();

        $packegist = new ClientMock();

        $command = new UseCommand($template, $prompts, $packegist);

        $this->assertSame($template, $command->getTemplate());
        $this->assertSame($prompts, $command->getPrompts());
        $this->assertSame($packegist, $command->getPackegist());
    }

    /**
     * @covers ::getPackageZipUrl
     */
    public function testGetPackageZipUrl()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $prompts = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\Prompts')
            ->disableOriginalConstructor()
            ->getMock();

        $packegist = new ClientMock();
        $packegist->queueResponse('packagist/package.json');

        $command = new UseCommand($template, $prompts, $packegist);

        $url = $command->getPackageZipUrl('clippings/package-template');
        $expected = 'https://api.github.com/repos/clippings/package-template/zipball/60c22c4aa0ae0afc3b0d7176a7154a9f2a005c0c';
        $this->assertEquals($expected, $url);

        $this->assertEquals(
            '/packages/clippings/package-template.json',
            $packegist->getHistory()->getLastRequest()->getUrl()
        );
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(['getPromptNames', 'putInto', 'open'])
            ->getMock();

        $prompts = $this
            ->getMockBuilder('CL\ComposerInit\Prompt\Prompts')
            ->disableOriginalConstructor()
            ->setMethods(['getValues'])
            ->getMock();

        $command = $this
            ->getMockBuilder('CL\ComposerInit\UseCommand')
            ->setConstructorArgs([$template, $prompts, new ClientMock()])
            ->setMethods(['getTemplate', 'getPrompts', 'getPackageZipUrl'])
            ->getMock();

        $command
            ->method('getPackageZipUrl')
            ->with('TEST_PACKAGE')
            ->willReturn('TEST_URL');

        $template
            ->method('open')
            ->with('TEST_URL');

        $template
            ->method('getPromptNames')
            ->willReturn(['author_name', 'title']);

        $template
            ->method('putInto')
            ->with(getcwd());

        $prompts
            ->method('getValues')
            ->with(
                ['author_name', 'title'],
                $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $this->isInstanceOf('Symfony\Component\Console\Helper\DialogHelper')
            )
            ->willReturn(['author_name' => 'VAL1', 'title' => 'VAL2']);

        $console = new Application();
        $console->add($command);

        $tester = new CommandTester($console->get('use'));

        $dialog = $command->getHelper('dialog');
        $dialog->setInputStream($this->getInputStream("y\n"));

        $tester->execute(['package' => 'TEST_PACKAGE']);

        $expected = <<<OUTPUT
Enter Template variables (Press enter for default):
Use These Variables:
  author_name: VAL1
  title: VAL2
Confirm? (Y/n):Done

OUTPUT;
        $this->assertEquals($expected, $tester->getDisplay());


        $dialog->setInputStream($this->getInputStream("n\n"));

        $tester->execute(['package' => 'TEST_PACKAGE']);

        $expected = <<<OUTPUT
Enter Template variables (Press enter for default):
Use These Variables:
  author_name: VAL1
  title: VAL2
Confirm? (Y/n):Aborted

OUTPUT;
        $this->assertEquals($expected, $tester->getDisplay());
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
