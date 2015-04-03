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
class CopyrightPrompt implements PromptInterface
{
    /**
     * Github Client
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
     * Get default values from different sources
     *
     *  - github organization
     *  - github user
     *  - git user
     *  - file owner
     *
     * @return array
     */
    public function getDefaults()
    {
        $defaults = [];
        $origin = $this->gitConfig->getOrigin();

        if (null !== $origin) {
            $repo = $this->github->get("/repos/{$origin}")->json();

            if (isset($repo['organization'])) {
                $organizaion = $this->github->get("/orgs/{$repo['organization']['login']}")->json();
                $defaults []= $organizaion['name'];
            }

            $owner = $this->github->get("/users/{$repo['owner']['login']}")->json();
            $defaults []= $owner['name'];
        }

        $defaults []= $this->gitConfig->get('user.name');
        $defaults []= get_current_user();

        return array_values(
            array_unique(
                array_filter(
                    $defaults
                )
            )
        );
    }

    /**
     * Prepend a string to all items in array
     *
     * @param  string $prepend
     * @param  array  $array
     * @return array
     */
    public function prependToArray($prepend, array $array)
    {
        return array_map(function($item) use ($prepend) {
            return $prepend.$item;
        }, $array);
    }

    /**
     * @param  OutputInterface $output
     * @param  DialogHelper    $dialog
     * @return array
     */
    public function getValues(OutputInterface $output, DialogHelper $dialog)
    {
        $defaults = $this->prependToArray(
            date('Y').', ',
            $this->getDefaults()
        );

        $value = $dialog->ask(
            $output,
            "<info>Copyright</info> ({$defaults[0]}): ",
            $defaults[0],
            $defaults
        );

        return [
            'copyright' => $value,
            'copyright_entity' => preg_replace('/^\d{4}, +/', '', $value),
        ];
    }
}

