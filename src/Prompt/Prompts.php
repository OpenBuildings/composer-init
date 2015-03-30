<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Pimple\Container;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Prompts
{
    private $container;

    public function __construct()
    {
        $this->container = new Container();

        $this->container['github'] = function () {
            return new Client('https://api.github.com');
        };

        $this->container['config'] = function () {
            return new GitConfig();
        };

        $this->container['inflector'] = function () {
            return new Inflector();
        };

        # Prompts
        # --------------------

        $this->container['prompt.author_email'] = function ($container) {
            return new AuthorEmailPrompt($container['github']);
        };

        $this->container['prompt.author_name'] = function ($container) {
            return new AuthorNamePrompt($container['github']);
        };

        $this->container['prompt.bugs'] = function ($container) {
            return new BugsPrompt($container['config'], $container['github']);
        };

        $this->container['prompt.copyright'] = function ($container) {
            return new CopyrightPrompt($container['config'], $container['github']);
        };

        $this->container['prompt.description'] = function ($container) {
            return new DescriptionPrompt($container['github']);
        };

        $this->container['prompt.php_namespace'] = function ($container) {
            return new PhpNamespacePrompt($container['config'], $container['inflector']);
        };

        $this->container['prompt.slack_notification'] = function () {
            return new SlackNotificationPrompt();
        };

        $this->container['prompt.title'] = function ($container) {
            return new TitlePrompt($container['config'], $container['config'], $container['inflector']);
        };
    }

    public function get($name)
    {
        return $this->container["prompt.{$name}"];
    }

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

