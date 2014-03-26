<?php

namespace CL\ComposerInit;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ZipArchive extends \ZipArchive
{
    public function getRootDir()
    {
        return $this->getNameIndex(0);
    }

    /**
     * @param string $dir
     * @param string $destination
     */
    public function extractDirTo($dir, $destination)
    {
        $fullDir = $dir.'/';

        $only = array();

        for ($i = 0; $i < $this->numFiles; $i++) {
            $name = $this->getNameIndex($i);
            if ($name !== $fullDir and strpos($name, $fullDir) === 0) {
                $only []= $name;
            }
        }

        $this->extractTo($destination, $only);
    }

    /**
     * @param string $file
     */
    public function includeFile($file)
    {
        $tmp = tmpfile();
        $metadata = stream_get_meta_data($tmp);

        file_put_contents($metadata['uri'], $this->getFromName($file));

        include $metadata['uri'];
    }
}
