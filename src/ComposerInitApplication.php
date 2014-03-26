<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Application;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ComposerInitApplication extends Application
{
    protected $github;
    protected $packegist;
    protected $configFile;

    /**
     * Constructor.
     *
     * @param string $name    The name of the application
     * @param string $version The version of the application
     *
     * @api
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->configFile = $_SERVER['HOME'].DIRECTORY_SEPARATOR.'.composer-init.json';

        $this->add(new SearchCommand());
        $this->add(new UseCommand());
        $this->add(new TokenCommand());

        $template = new TemplateHelper();
        $template->setApplication($this);

        $this->getHelperSet()->set($template);
    }

    public function getConfigFile()
    {
        return $this->configFile;
    }

    public function getConfig()
    {
        if (is_file($this->getConfigFile())) {
            return json_decode(file_get_contents($this->getConfigFile()), true);
        } else {
            return null;
        }
    }

    public function setConfig(array $config)
    {
        $content = defined('JSON_PRETTY_PRINT')
            ? json_encode($config, JSON_PRETTY_PRINT)
            : json_encode($config);

        file_put_contents($this->getConfigFile(), $content);

        return $this;
    }

    public function getGithubToken()
    {
        $config = $this->getConfig();

        return isset($config['token']) ? $config['token'] : null;
    }

    public function setGithubToken($token)
    {
        $config = $this->getConfig();
        $config['token'] = $token;

        $this->setConfig($config);

        return $this;
    }

    public function getGithub()
    {
        if (! $this->github) {
            $this->github = new \Github\Client();

            if (($token = $this->getGithubToken())) {
                $this->github->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
            }
        }

        return $this->github;
    }
}
