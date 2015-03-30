<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use CL\ComposerInit\SearchCommand;
use GuzzleHttp\Client;

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
        $client = new Client();
        $command = new SearchCommand($client);

        $this->assertSame($client, $command->getPackegist());
    }

    /**
     * @covers ::getTemplates
     */
    public function testGetTemplates()
    {
        $client = new PackagistMock();
        $client->queueResponse('list.json');

        $command = new SearchCommand($client);

        $templates = $command->getTemplates();
        $expected = [
            'clippings/package-template',
            'harp-orm/harp-template',
            'openbuildings/jam-template',
        ];
        $this->assertEquals($expected, $templates);

        $this->assertEquals(
            '/packages/list.json?type=composer-init-template',
            $client->getHistory()->getLastRequest()->getUrl()
        );
    }

    /**
     * @covers ::filterWith
     */
    public function testFilterWith()
    {
        $templates = ['value1', 'value2', 'other'];
        $expected = ['value1', 'value2'];

        $command = new SearchCommand(new Client());

        $filtered = $command->filterWith($templates, 'value');
        $this->assertEquals($expected, $filtered);
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $client = new PackagistMock();
        $client
            ->queueResponse('list.json')
            ->queueResponse('list.json')
            ->queueResponse('list.empty.json');

        $command = new SearchCommand($client);

        $tester = new CommandTester($command);

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
