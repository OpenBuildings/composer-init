<?php

namespace CL\ComposerInit;

use GuzzleHttp\Client;
use CL\ComposerInit\Prompt\Prompts;
use CL\ComposerInit\SearchCommand;
use CL\ComposerInit\UseCommand;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var Container
     */
    private $app;

    public function __construct($tokenFile = '~/.composer-init')
    {
        parent::__construct('Composer Init', '0.3');

        $packegist = new Client(['base_uri' => 'https://packagist.org']);
        $token = new Token($tokenFile);

        $githubOptions = ['base_uri' => 'https://api.github.com'];

        if (null !== $token->get()) {
            $githubOptions['query']['access_token'] = $token->get();
        }

        $github = new Client($githubOptions);

        $gitConfig = new GitConfig();
        $inflector = new Inflector();
        $prompts = new Prompts($gitConfig, $github, $inflector);
        $template = new Template($github);

        $this->add(new SearchCommand($packegist));
        $this->add(new UseCommand($template, $prompts, $packegist));
        $this->add(new TokenCommand($token));
    }
}
