<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $json = Curl::getJSON('https://packagist.org/packages/packages/list.json?q=composer-init-template');

        $templates = $json['packageNames'];

        if (($query = $input->getArgument('name'))) {
            $templates = array_filter($json['packageNames'], function ($name) use ($query) {
                return strpos($name, $query) !== false;
            });
        }

        if (! count($templates)) {
            $output->writeln("<error>No templates found</error>");
        } else {
            $output->writeln('Available Init Templates:');
            foreach ($templates as $package) {
                $output->writeln("  <info>{$package}</info>");
            }
        }
    }
}
