<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use CL\ComposerInit\GitConfig;
use RuntimeException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PackageNamePrompt implements PromptInterface
{
    /**
     * @var GitConfig
     */
    private $gitConfig;

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
     * @return array
     */
    public function getDefault()
    {
        return $this->gitConfig->getOrigin();
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
            "<info>Package Name</info> ({$default}): ",
            $default
        );

        $parts = explode('/', $default);
        $owner = $parts[0];
        $title = isset($parts[1]) ? $parts[1] : $default;

        return [
            'package_name' => $value,
            'package_owner' => $owner,
            'package_title' => $title,
        ];
    }
}
