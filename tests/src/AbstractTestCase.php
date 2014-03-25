<?php

namespace CL\ComposerInit\Test;

use PHPUnit_Framework_TestCase;
use CL\EnvBackup\ServerParam;
use CL\EnvBackup\FileParam;
use CL\EnvBackup\Env;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public $env;

    public function setUp()
    {
        parent::setUp();

        $this->env = new Env(array(
            new ServerParam(
                'HOME',
                rtrim($this->getTestsDir(), '/'),
                ServerParam::CLI
            ),
            new FileParam(
                $this->getTestsDir().'.composer-init.json',
                '{"token":"123456789012345678901234567890"}'
            ),
        ));

        $this->env->apply();
    }

    public function tearDown()
    {
        if ($this->env) {
            $this->env->restore();
        }

        parent::tearDown();
    }

    public function getTestsDir()
    {
        return dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
