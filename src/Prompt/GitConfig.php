<?php

namespace CL\ComposerInit\Prompt;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class GitConfig
{
    /**
     * @param  string $option
     * @return string
     */
    public function get($option)
    {
        return $this->shell("git option $option");
    }

    /**
     * @param  string $command
     * @return string
     */
    public function shell($command)
    {
        return trim(shell_exec($command));
    }

    /**
     * @return string|null
     */
    public function getOrigin()
    {
        $origin = $this->get('remote.origin.url');

        if (null !== $origin) {
            preg_match('/^.*github.com[:\/](.*).git$/', $origin, $matches);

            return $matches[1];
        }
    }
}


