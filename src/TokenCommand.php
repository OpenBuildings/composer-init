<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TokenCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('token')
            ->setDescription('Set the github token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $token = $dialog->askHiddenResponseAndValidate(
            $output,
            'Please enter the github token for this application: ',
            function ($value) {
                if (strlen($value) !== 40) {
                    throw new \Exception('Must be a 40 letter token');
                }
                return $value;
            },
            5,
            false
        );

        $this->getApplication()->setGithubToken($token);

        $output->writeln("Token set to ".$this->getApplication()->getConfigFile());
    }
}
