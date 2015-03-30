<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class AuthorNamePrompt implements PromptInterface
{
    /**
     * @var GitConfig
     */
    private $gitConfig;

    /**
     * @param GitConfig $gitConfig
     */
    public function __construct(GitConfig $gitConfig)
    {
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
     * @return string
     */
    public function getDefault()
    {
        return $this->gitConfig->get('user.name');
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
            "<info>Author name</info> ({$default}): ",
            $default
        );

        return ['author_name' => $value];
    }
}
