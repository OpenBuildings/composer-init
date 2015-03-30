<?php

use Symfony\Component\Console\Application;
use GuzzleHttp\Client;
use CL\ComposerInit\SearchCommand;
use CL\ComposerInit\UseCommand;


$console = new Application('Composer Init', '2.0');

$packegist = new Client('https://packagist.org');
$console->add(new SearchCommand($packegist));
$console->add(new UseCommand($packegist));

return $console;
