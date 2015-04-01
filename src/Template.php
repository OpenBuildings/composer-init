<?php

namespace CL\ComposerInit;

use ZipArchive;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Template
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @var ZipArchive
     */
    private $zip;

    /**
     * @var resource
     */
    private $zipFile;

    /**
     * @var string
     */
    private $root;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->zipFile = tmpfile();

        $meta = stream_get_meta_data($this->zipFile);
        file_put_contents($meta['uri'], fopen($url, 'r'));

        $this->zip = new ZipArchive();
        $this->zip->open($meta['uri'], ZIPARCHIVE::CHECKCONS);
        $this->root = $this->zip->getNameIndex(0);
    }

    /**
     * @return ZipArchive
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * set values, convert keys to template kyes
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Return contents of prompts.json file
     * @return array
     */
    public function getPromptNames()
    {
        return (array) json_decode($this->zip->getFromName($this->root.'prompts.json'), true);
    }

    /**
     * Populate content with set template values
     * @param  string $content
     * @return string
     */
    public function populateValues($content)
    {
        $values = [];

        foreach ($this->values as $key => $value) {
            $values['{% '.$key.' %}'] = $value;
        }

        return strtr($content, $values);
    }

    /**
     * @param  string $filename
     * @param  string $content
     */
    public function putFile($filename, $content)
    {
        file_put_contents($filename, $content);
    }

    /**
     * @param  string $dirname
     */
    public function putDir($dirname)
    {
        if (false === file_exists($dirname)) {
            mkdir($dirname, 0777);
        }
    }

    /**
     * Put all file in "root" dir in the template zip into a given directory
     * Replace templates with values
     * @param  string $dir
     */
    public function putInto($dir)
    {
        for ($i = 0; $i < $this->zip->numFiles; $i++)
        {
            $name = $this->zip->getNameIndex($i);
            $rootDir = preg_quote($this->root, '/');

            if (preg_match("/^{$rootDir}root(\/.+)/", $name, $matches)) {
                $filename = $matches[1];

                if (substr($filename, -1) === '/') {
                    $this->putDir($dir.$filename);
                } else {
                    $content = $this->populateValues($this->zip->getFromName($name));
                    $this->putFile($dir.$filename, $content);
                }
            }
        }
    }
}
