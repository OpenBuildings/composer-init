<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use GuzzleHttp\Client;
use Pimple\Container;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Prompts
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function __construct()
    {
        $this->container = new Container();

        $this->container['github'] = function () {
            return new Client(['base_url' => 'https://api.github.com']);
        };

        $this->container['git_config'] = function () {
            return new GitConfig();
        };

        $this->container['inflector'] = function () {
            return new Inflector();
        };

        # Prompts
        # --------------------

        $this->container['prompt.author_email'] = function ($container) {
            return new AuthorEmailPrompt($container['git_config']);
        };

        $this->container['prompt.author_name'] = function ($container) {
            return new AuthorNamePrompt($container['git_config']);
        };

        $this->container['prompt.bugs'] = function ($container) {
            return new BugsPrompt($container['git_config'], $container['github']);
        };

        $this->container['prompt.copyright'] = function ($container) {
            return new CopyrightPrompt($container['git_config'], $container['github']);
        };

        $this->container['prompt.description'] = function ($container) {
            return new DescriptionPrompt($container['git_config'], $container['github']);
        };

        $this->container['prompt.php_namespace'] = function ($container) {
            return new PhpNamespacePrompt($container['git_config'], $container['inflector']);
        };

        $this->container['prompt.slack_notification'] = function () {
            return new SlackNotificationPrompt();
        };

        $this->container['prompt.title'] = function ($container) {
            return new TitlePrompt($container['git_config'], $container['github'], $container['inflector']);
        };
    }

    /**
     * @param  string $name
     * @return PromptInterface
     */
    public function get($name)
    {
        return $this->container["prompt.{$name}"];
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

