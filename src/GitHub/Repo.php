<?php

namespace CL\ComposerInit\GitHub;

use CL\ComposerInit\Curl;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Repo
{
    protected static $oauthToken;

    public static function isValidUrl($url)
    {
        $parts = parse_url($url);

        return (isset($parts) and $parts['host'] == 'github.com');
    }

    public static function newFromOrigin($url)
    {
        preg_match('/^git@github.com:(.*).git$/', $url, $matches);

        return new Repo($matches[1]);
    }

    protected $data;
    protected $full_name;
    protected $owner;
    protected $organization;

    function __construct($full_name)
    {
        $this->full_name = $full_name;
    }

    public function getData()
    {
        if ( ! isset($this->data))
        {
            $repoJSON = Curl::get('https://api.github.com/repos/'.$this->full_name);
            $this->data = json_decode($repoJSON);
        }

        return $this->data;
    }

    public function getOrganization()
    {
        if ( ! isset($this->organization))
        {
            $this->organization = new User($this->getData()->organization->login);
        }

        return $this->organization;
    }

    public function getOwner()
    {
        if ( ! isset($this->owner))
        {
            $this->owner = new User($this->getData()->owner->login);
        }

        return $this->owner;
    }

    public function getOrganizationName()
    {
        return $this->getOrganization()->getName();
    }

    public function getOwnerName()
    {
        return $this->getOwner()->getName();
    }

    public function getDescription()
    {
        return $this->getData()->description;
    }

    public function getName()
    {
        return $this->getData()->name;
    }

    public function getHtmlUrl()
    {
        return $this->getData()->html_url;
    }

    public function getFullName()
    {
        return $this->full_name;
    }
}
