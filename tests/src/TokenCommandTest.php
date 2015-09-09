<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use CL\ComposerInit\TokenCommand;
use Symfony\Component\Console\Application;

/**
 * @coversDefaultClass CL\ComposerInit\TokenCommand
 */
class TokenCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getToken
     * @covers ::configure
     */
    public function testConstruct()
    {
        $token = $this
            ->getMockBuilder('CL\ComposerInit\Token')
            ->disableOriginalConstructor()
            ->getMock();

        $command = new TokenCommand($token);

        $this->assertSame($token, $command->getToken());
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $token = $this
            ->getMockBuilder('CL\ComposerInit\Token')
            ->disableOriginalConstructor()
            ->setMethods(['set', 'getFilename'])
            ->getMock();

        $token
            ->method('set')
            ->with('NEW_TOKEN');

        $token
            ->method('getFilename')
            ->willReturn('TOKEN_FILENAME');

        $command = new TokenCommand($token);

        $console = new Application();
        $console->add($command);

        $tester = new CommandTester($console->get('token'));

        $tester->execute(['token' => 'NEW_TOKEN']);

        $expected = <<<OUTPUT
Token saved to TOKEN_FILENAME

OUTPUT;
        $this->assertEquals($expected, $tester->getDisplay());
    }
}
