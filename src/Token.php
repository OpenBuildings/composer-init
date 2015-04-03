<?php

namespace CL\ComposerInit;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Token
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function get()
    {
        if (file_exists($this->filename)) {
            return file_get_contents($this->filename);
        }
    }

    /**
     * @param string $token
     */
    public function set($token)
    {
        file_put_contents($this->filename, $token);

        return $this;
    }
}
