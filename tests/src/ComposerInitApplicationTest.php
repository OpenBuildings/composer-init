<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\ComposerInitApplication;

class ComposerInitApplicationTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\ComposerInitApplication::__construct
     * @covers CL\ComposerInit\ComposerInitApplication::getConfigFile
     */
    public function testConstruct()
    {
        $application = new ComposerInitApplication();

        $this->assertEquals($this->getTestsDir().'.composer-init.json', $application->getConfigFile());

        $this->assertTrue($application->getHelperSet()->has('template'));
    }

    /**
     * @covers CL\ComposerInit\ComposerInitApplication::getConfig
     */
    public function testGetConfig()
    {
        $application = new ComposerInitApplication();

        $expected = array(
            'token' => '123456789012345678901234567890',
        );

        $this->assertEquals($expected, $application->getConfig());

        rename($application->getConfigFile(), $application->getConfigFile().'.old');

        $this->assertEquals(null, $application->getConfig());

        rename($application->getConfigFile().'.old', $application->getConfigFile());
    }

    /**
     * @covers CL\ComposerInit\ComposerInitApplication::setConfig
     */
    public function testSetConfig()
    {
        $application = new ComposerInitApplication();

        $config = array(
            'token' => '223456789012345678901234567890',
        );

        $application->setConfig($config);

        $this->assertEquals($config, $application->getConfig());
    }

    /**
     * @covers CL\ComposerInit\ComposerInitApplication::getGithubToken
     * @covers CL\ComposerInit\ComposerInitApplication::setGithubToken
     */
    public function testGetGithubToken()
    {
        $application = new ComposerInitApplication();

        $expected = '123456789012345678901234567890';

        $this->assertEquals($expected, $application->getGithubToken());

        $expected = '223456789012345678901234567890';

        $application->setGithubToken($expected);

        $this->assertEquals($expected, $application->getGithubToken());
    }

    /**
     * @covers CL\ComposerInit\ComposerInitApplication::getGithub
     */
    public function testGetGithub()
    {
        $application = new ComposerInitApplication();

        $github = $application->getGithub();

        $this->assertInstanceOf('Github\Client', $github);
    }

}
