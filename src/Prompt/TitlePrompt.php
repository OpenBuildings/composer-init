<?php

namespace CL\ComposerInit\Prompt;

use GuzzleHttp\Client;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TitlePrompt implements PromptInterface
{
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

    /**
     * @param GitConfig $gitConfig
     * @param Client    $github
     * @param Inflector $inflector
     */
    public function __construct(GitConfig $gitConfig, Client $github, Inflector $inflector)
    {
        $this->gitConfig = $gitConfig;
        $this->github = $github;
        $this->inflector = $inflector;
    }

    /**
     * @return GitConfig
     */
    public function getGitConfig()
    {
        return $this->gitConfig;
    }

    /**
     * @return Client
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * @return Inflector
     */
    public function getInflector()
    {
        return $this->inflector;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        $origin = $this->gitConfig->getOrigin();

        if (null !== $origin) {
            $repo = $this->github->get("/repos/{$origin}")->json();
            return $this->inflector->title($repo['name']);
        }

        return $this->inflector->title(getcwd());
    }

    /**
     * @param  OutputInterface $output
     * @param  DialogHelper    $dialog
     * @return array
     */
    public function getValues(OutputInterface $output, DialogHelper $dialog)
    {
        $default = $this->getDefault();

        $value = $dialog->ask(
            $output,
            "<info>Title</info> ({$default}): ",
            $default
        );

        return [
            'title' => $value,
            'title_underline' => str_pad('', mb_strlen($value), '='),
        ];
    }
}


