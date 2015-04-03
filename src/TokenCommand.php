<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TokenCommand extends Command
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        parent::__construct();

        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function configure()
    {
        $this
            ->setName('token')
            ->setDescription('Set a token for github')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'Github application token'
            );
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');

        $this->token->set($token);

        $output->writeln("Token saved to {$this->token->getFilename()}");
    }
}
