<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Token;

/**
 * @coversDefaultClass CL\ComposerInit\Template
 */
class TokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getFilename
     */
    public function testConstruct()
    {
        $template = new Token('FILENAME');

        $this->assertSame('FILENAME', $template->getFilename());
    }
}
