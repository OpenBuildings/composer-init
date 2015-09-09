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

        $this->assertInstanceOf(
            'CL\ComposerInit\TokenCommand',
            $app->get('token')
        );

        $this->assertEquals(
            null,
            $app->get('token')->getToken()->get(),
            'Should not have a token saved at default location'
        );
    }

    /**
     * @covers ::__construct
     */
    public function testConstructWithToken()
    {
        $file = __DIR__.'/composer-token-test';

        file_put_contents($file, 'TEST_APP_TOKEN');

        $app = new Application($file);

        $this->assertEquals(
            'TEST_APP_TOKEN',
            $app->get('token')->getToken()->get(),
            'Token content should be from the passed file'
        );

        unlink($file);
    }
}
