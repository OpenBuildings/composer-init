<?php

namespace CL\ComposerInit;

use GuzzleHttp\Client;
use CL\ComposerInit\SearchCommand;
use CL\ComposerInit\UseCommand;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var Client
     */
    private $packegist;

    public function __construct()
    {
        parent::__construct('Composer Init', '0.3');

        $this->packegist = new Client(['base_url' => 'https://packagist.org']);

        $this->add(new SearchCommand($this->packegist));
        $this->add(new UseCommand($this->packegist));
    }

    /**
     * @return Client
     */
    public function getPackegist()
    {
        return $this->packegist;
    }
}
