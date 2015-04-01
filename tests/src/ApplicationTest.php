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
     * @covers ::getPackegist
     */
    public function testConstruct()
    {
        $app = new Application();

        $this->assertInstanceOf(
            'GuzzleHttp\Client',
            $app->getPackegist()
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\SearchCommand',
            $app->get('search')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\UseCommand',
            $app->get('use')
        );
    }
}
