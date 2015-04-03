<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use CL\ComposerInit\GitConfig;
use CL\ComposerInit\Inflector;
use RuntimeException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PhpNamespacePrompt implements PromptInterface
{
    /**
     * @var GitConfig
     */
    private $gitConfig;

    /**
     * @var Inflector
     */
    private $inflector;

    public function __construct(GitConfig $gitConfig, Inflector $inflector)
    {
        $this->gitConfig = $gitConfig;
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
     * @return Inflector
     */
    public function getInflector()
    {
        return $this->inflector;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        $origin = $this->gitConfig->getOrigin();
        if (null !== $origin) {
            list($vendor, $name) = explode('/', $origin);

            return [
                $this->inflector->titlecase($vendor).'\\'.$this->inflector->titlecase($name),
                $this->inflector->initials($vendor).'\\'.$this->inflector->titlecase($name),
            ];
        } else {
            return [];
        }
    }

    /**
     * @param  OutputInterface $output
     * @param  DialogHelper    $dialog
     * @return array
     */
    public function getValues(OutputInterface $output, DialogHelper $dialog)
    {
        $defaults = $this->getDefaults();

        $value = $dialog->askAndValidate(
            $output,
            "<info>PHP Namespace</info> ({$defaults[0]}): ",
            function ($namespace) {
                if (preg_match("/^([\w\\\\]+)*\w+$/", $namespace)) {
                    return $namespace;
                }

                throw new RuntimeException(sprintf(
                    '%s is not a valid namespace',
                    $namespace
                ));
            },
            false,
            reset($defaults),
            $defaults
        );

        return [
            'php_namespace' => $value,
            'php_namespace_escaped' => addslashes($value),
        ];
    }
}


