<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Token;

/**
 * @coversDefaultClass CL\ComposerInit\Token
 */
class TokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getFilename
     */
    public function testConstruct()
    {
        $token = new Token('FILENAME');

        $this->assertSame('FILENAME', $token->getFilename());
    }

    /**
     * @covers ::set
     * @covers ::get
     */
    public function testSetGet()
    {
        $file = __DIR__.'/composer-token-test';

        if (file_exists($file)) {
            unlink($file);
        }

        $token = new Token($file);

        $this->assertSame(null, $token->get(), 'Should return null if no token file');

        $token->set('TEST_TOKEN');

        $this->assertEquals('TEST_TOKEN', file_get_contents($file), 'Token file should be created');

        $otherToken = new Token($file);

        $this->assertEquals('TEST_TOKEN', $otherToken->get(), 'Token should be read by file');

        unlink($file);
    }
}
