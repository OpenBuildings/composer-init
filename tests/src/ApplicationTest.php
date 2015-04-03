<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Application;

/**
 * @coversDefaultClass CL\ComposerInit\Application
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $app = new Application();

        $this->assertInstanceOf(
            'CL\ComposerInit\SearchCommand',
            $app->get('search')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\UseCommand',
            $app->get('use')
        );
    }

    /**
     * @covers ::getComposerToken
     */
    public function testGetCompoerToken()
    {
        $app = new Application();

        $token = $app->getComposerToken(__DIR__.'/../empty.json');

        $this->assertNull($token);

        $token = $app->getComposerToken(__DIR__.'/../auth.json');

        $this->assertEquals('TEST_TOKEN', $token);
    }
}
