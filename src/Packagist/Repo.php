<?php

namespace CL\ComposerInit\Packagist;

use CL\ComposerInit\Curl;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Repo
{
    protected $data;
    protected $name;

    function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        if (! $this->data) {
            $json = Curl::get("https://packagist.org/packages/{$this->name}.json");
            $this->data = json_decode($json, true);
        }
        return $this->data;
    }

    public function getDistUrl($version = 'dev-master')
    {
        $data = $this->getData();
        return $data['package']['versions'][$version]['dist']['url'];
    }
}
