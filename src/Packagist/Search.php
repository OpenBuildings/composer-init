<?php

namespace CL\ComposerInit\Packagist;

use CL\ComposerInit\Curl;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Search
{
    protected $data;
    protected $type;
    protected $query;

    function __construct($type, $query = null)
    {
        $this->type = $type;
        $this->query = $query;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getData()
    {
        if (! $this->data) {
            $json = Curl::get("https://packagist.org/packages/list.json?type={$this->type}");
            $this->data = json_decode($json);

            if ($this->query) {
                $query = $this->query;

                $this->data->packageNames = array_filter($this->data->packageNames, function($name) use ($query) {
                    return strpos($name, $query) !== false;
                });
            }
        }
        return $this->data;
    }

    public function getResults()
    {
        return $this->getData()->packageNames;
    }
}
