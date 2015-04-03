<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\GitConfig;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use RuntimeException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class AuthorEmailPrompt implements PromptInterface
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
        return $this->gitConfig->get('user.email');
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
            "<info>Author email</info> ({$default}): ",
            function ($email) {
                if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Not a valid email');
                }

                return $email;
            },
            false,
            $default
        );

        return ['author_email' => $value];
    }
}
