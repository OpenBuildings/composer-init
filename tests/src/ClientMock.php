<?php

namespace CL\ComposerInit\Test;

use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;

class ClientMock extends Client
{
    private $history;
    private $mock;

    public function __construct()
    {
        parent::__construct();

        $this->mock = new Mock();
        $this->history = new History();

        $this->getEmitter()->attach($this->history);
        $this->getEmitter()->attach($this->mock);
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function getMock()
    {
        return $this->mock;
    }

    public function queueResponse($fileName)
    {
        $response = file_get_contents(__DIR__.'/../responses/'.$fileName);

        $this->mock->addResponse(
            new Response(200, [], Stream::factory($response))
        );

        return $this;
    }
}
