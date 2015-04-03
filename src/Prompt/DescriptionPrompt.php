<?php

namespace CL\ComposerInit\Prompt;

use GuzzleHttp\Client;
use CL\ComposerInit\GitConfig;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class DescriptionPrompt implements PromptInterface
{
    /**
     * Github Client
     *
     * @var Client
     */
    private $github;


    /**
     * @var GitConfig
     */
    private $gitConfig;

    /**
     * @param GitConfig $gitConfig
     * @param Client    $github
     */
    public function __construct(GitConfig $gitConfig, Client $github)
    {
        $this->gitConfig = $gitConfig;
        $this->github = $github;
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
     * @return string|null
     */
    public function getDefault()
    {
        $origin = $this->gitConfig->getOrigin();

        if (null !== $origin) {
            $repo = $this->github->get("/repos/{$origin}")->json();
            return $repo['description'];
        }
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
            "<info>Description</info> ({$default}): ",
            $default
        );

        return [
            'description' => $value,
        ];
    }
}

