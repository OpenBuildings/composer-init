<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CL\ComposerInit\Packagist;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class SearchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('search')
            ->setDescription('Search available composer init templates')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Filter by name'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $search = new Packagist\Search('composer-init-template', $input->getArgument('name'));


        if (! count($search->getResults()))
        {
            $output->writeln("<error>No templates found</error>");
        }
        else
        {
            $output->writeln('Available Init Templates:');
            foreach ($search->getResults() as $package) {
                $output->writeln("  <info>{$package}</info>");
            }
        }
    }
}
