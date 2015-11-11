<?php

namespace CL\ComposerInit\Test;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

class ClientMock extends Client
{
    private $container = [];
    private $mock;

    public function __construct()
    {
        $history = Middleware::history($this->container);

        $this->mock = new MockHandler();

        $stack = HandlerStack::create($this->mock);
        // Add the history middleware to the handler stack.
        $stack->push($history);

        parent::__construct(['handler' => $stack]);
    }

    public function getHistory()
    {
        return $this->container;
    }

    public function getMock()
    {
        return $this->mock;
    }

    public function queueResponse($fileName)
    {
        $response = file_get_contents(__DIR__.'/../responses/'.$fileName);

        $this->mock->append(
            new Response(200, [], Psr7\stream_for($response))
        );

        return $this;
    }
}
