<?php

namespace CL\ComposerInit\GitHub;

use CL\ComposerInit\Curl;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class User
{
    protected $data;
    protected $login;

    function __construct($login)
    {
        $this->login = $login;
    }

    public function getData()
    {
        if ( ! isset($this->data))
        {
            $userJSON = Curl::get('https://api.github.com/users/'.$this->login);
            $this->data = json_decode($userJSON);
        }

        return $this->data;
    }

    public function getName()
    {
        return $this->getData()->name;
    }
}
