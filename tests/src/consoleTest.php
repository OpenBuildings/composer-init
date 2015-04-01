<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;

class consoleTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $console = require __DIR__.'/../../src/console.php';

        $this->assertInstanceOf(
            'Symfony\Component\Console\Application',
            $console
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\SearchCommand',
            $console->get('search')
        );

        $this->assertInstanceOf(
            'CL\ComposerInit\UseCommand',
            $console->get('use')
        );
    }
}
