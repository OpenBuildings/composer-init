<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use CL\ComposerInit\SearchCommand;
use Symfony\Component\Console\Application;

/**
 * @coversDefaultClass CL\ComposerInit\SearchCommand
 */
class SearchCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getPackegist
     * @covers ::configure
     */
    public function testConstruct()
    {
        $client = new ClientMock();
        $command = new SearchCommand($client);

        $this->assertSame($client, $command->getPackegist());
    }

    /**
     * @covers ::getTemplates
     */
    public function testGetTemplates()
    {
        $client = new ClientMock();
        $client->queueResponse('packagist/list.json');

        $command = new SearchCommand($client);

        $templates = $command->getTemplates();
        $expected = [
            'clippings/package-template',
            'harp-orm/harp-template',
            'openbuildings/jam-template',
        ];
        $this->assertEquals($expected, $templates);

        $history = $client->getHistory();

        $this->assertEquals(
            '/packages/list.json?type=composer-init-template',
            (string) $history[0]['request']->getUri()
        );
    }

    /**
     * @covers ::filterWith
     */
    public function testFilterWith()
    {
        $templates = ['value1', 'value2', 'other'];
        $expected = ['value1', 'value2'];

        $command = new SearchCommand(new ClientMock());

        $filtered = $command->filterWith($templates, 'value');
        $this->assertEquals($expected, $filtered);
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $client = new ClientMock();
        $client
            ->queueResponse('packagist/list.json')
            ->queueResponse('packagist/list.json')
            ->queueResponse('packagist/list.empty.json');

        $command = new SearchCommand($client);

        $console = new Application();
        $console->add($command);

        $tester = new CommandTester($console->get('search'));

        $tester->execute([]);
        $expected = <<<RESPONSE
Available Init Templates:
  clippings/package-template
  harp-orm/harp-template
  openbuildings/jam-template

RESPONSE;
        $this->assertEquals($expected, $tester->getDisplay());


        $tester->execute(['filter' => 'clip']);
        $expected = <<<RESPONSE
Available Init Templates:
  clippings/package-template

RESPONSE;
        $this->assertEquals($expected, $tester->getDisplay());


        $expected = <<<RESPONSE
No templates found

RESPONSE;
        $tester->execute([]);
        $this->assertEquals($expected, $tester->getDisplay());
    }
}
