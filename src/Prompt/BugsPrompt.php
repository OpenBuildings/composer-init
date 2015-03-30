<?php

namespace CL\ComposerInit\Prompt;

use GuzzleHttp\Client;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use RuntimeException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class BugsPrompt implements PromptInterface
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
        $this->github = $github;
        $this->gitConfig = $gitConfig;
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
     * @return string|null
     */
    public function getDefault()
    {
        $origin = $this->gitConfig->getOrigin();

        if ($origin) {
            $repo = $this->github->get("/repos/{$origin}")->json();
            return "{$repo['html_url']}/issues/new";
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

        $value = $dialog->askAndValidate(
            $output,
            "<info>Issues url</info> ({$default}): ",
            function ($email) {
                if (false === filter_var($email, FILTER_VALIDATE_URL)) {
                    throw new RuntimeException('Not a valid url');
                }

                return $email;
            },
            false,
            $default
        );

        return ['bugs' => $value];
    }
}
