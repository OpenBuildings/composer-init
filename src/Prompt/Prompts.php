<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use GuzzleHttp\Client;
use CL\ComposerInit\GitConfig;
use CL\ComposerInit\Inflector;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Prompts
{
    /**
     * @var PromptInterface[]
     */
    private $prompts;

    /**
     * @var Client
     */
    private $github;

    /**
     * @var GitConfig
     */
    private $gitConfig;

    /**
     * @var Inflector
     */
    private $inflector;

    public function __construct(GitConfig $gitConfig, Client $github, Inflector $inflector)
    {
        $this->github = $github;
        $this->gitConfig = $gitConfig;
        $this->inflector = $inflector;

        $this->add('author_email', new AuthorEmailPrompt($this->gitConfig));
        $this->add('author_name', new AuthorNamePrompt($this->gitConfig));
        $this->add('bugs', new BugsPrompt($this->gitConfig, $this->github));
        $this->add('copyright', new CopyrightPrompt($this->gitConfig, $this->github));
        $this->add('description', new DescriptionPrompt($this->gitConfig, $this->github));
        $this->add('php_namespace', new PhpNamespacePrompt($this->gitConfig, $this->inflector));
        $this->add('package_name', new PackageNamePrompt($this->gitConfig));
        $this->add('slack_notification', new SlackNotificationPrompt());
        $this->add('title', new TitlePrompt($this->gitConfig, $this->github, $this->inflector));
    }

    /**
     * @return Client
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * @return GitConfig
     */
    public function getGitConfig()
    {
        return $this->gitConfig;
    }

    /**
     * @return Inflector
     */
    public function getInflector()
    {
        return $this->inflector;
    }

    /**
     * @return Container
     */
    public function get($name)
    {
        return $this->prompts[$name];
    }

    public function add($name, PromptInterface $prompt)
    {
        $this->prompts[$name] = $prompt;
    }

    /**
     * @param  array           $prompts
     * @param  OutputInterface $output
     * @param  DialogHelper    $dialog
     * @return array
     */
    public function getValues(array $prompts, OutputInterface $output, DialogHelper $dialog)
    {
        $values = [];

        foreach ($prompts as $name) {
            $container = $this->get($name);

            $values = array_merge(
                $values,
                $container->getValues($output, $dialog)
            );
        }

        return $values;
    }
}

