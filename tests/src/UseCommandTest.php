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
     * @covers ::configure
     */
    public function testConstruct()
    {
        $client = new ClientMock();
        $command = new UseCommand($client);

        $this->assertSame($client, $command->getPackegist());
    }

    /**
     * @covers ::getPackageZipUrl
     */
    public function testGetPackageZipUrl()
    {
        $client = new ClientMock();
        $client->queueResponse('packagist/package.json');

        $command = new UseCommand($client);

        $url = $command->getPackageZipUrl('clippings/package-template');
        $expected = 'https://api.github.com/repos/clippings/package-template/zipball/60c22c4aa0ae0afc3b0d7176a7154a9f2a005c0c';
        $this->assertEquals($expected, $url);

        $this->assertEquals(
            '/packages/clippings/package-template.json',
            $client->getHistory()->getLastRequest()->getUrl()
        );
    }

    /**
     * @covers ::getPrompts
     */
    public function testGetPrompts()
    {
        $command = new UseCommand(new ClientMock());

        $prompts = $command->getPrompts();

        $this->assertInstanceOf('CL\ComposerInit\Prompt\Prompts', $prompts);
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplate()
    {
        $command = $this
            ->getMockBuilder('CL\ComposerInit\UseCommand')
            ->disableOriginalConstructor()
            ->setMethods(['getPackageZipUrl'])
            ->getMock();

        $command
            ->method('getPackageZipUrl')
            ->with('TEST_PACKAGE')
            ->willReturn('file://'.__DIR__.'/../test.zip');

        $template = $command->getTemplate('TEST_PACKAGE');

        $this->assertInstanceOf('CL\ComposerInit\Template', $template);

        $this->assertEquals(
            'clippings-package-template-971a36f/',
            $template->getRoot()
        );
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $command = $this
            ->getMockBuilder('CL\ComposerInit\UseCommand')
            ->setConstructorArgs([new ClientMock()])
            ->setMethods(['getTemplate', 'getPrompts'])
            ->getMock();

        $template = $this
            ->getMockBuilder('CL\ComposerInit\Template')
            ->disableOriginalConstructor()
            ->setMethods(['getPromptNames', 'putInto'])
            ->getMock();

        $prompts = $this
            ->getMockBuilder('CL\ComposerInit\Prompts')
            ->setMethods(['getValues'])
            ->getMock();

        $command
            ->method('getTemplate')
            ->with('TEST_PACKAGE')
            ->willReturn($template);

        $command
            ->method('getPrompts')
            ->willReturn($prompts);

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
