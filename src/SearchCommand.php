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
class SearchCommand extends Command
{
    /**
     * @var Client
     */
    private $packagist;

    /**
     * @param Client $packagist
     */
    public function __construct(Client $packagist)
    {
        parent::__construct();

        $this->packagist = $packagist;
    }

    /**
     * @return Client
     */
    public function getPackagist()
    {
        return $this->packagist;
    }

    protected function configure()
    {
        $this
            ->setName('search')
            ->setDescription('Search available composer init templates')
            ->addArgument(
                'filter',
                InputArgument::OPTIONAL,
                'Filter by name'
            );
        ;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $response = $this->packagist->get(
            '/packages/list.json',
            ['query' => ['type' => 'composer-init-template']]
        );

        $list = json_decode($response->getBody(), true);

        return (array) $list['packageNames'];
    }

    /**
     * @param  array  $array
     * @param  string $filter
     * @return array
     */
    public function filterWith(array $array, $filter)
    {
        return array_filter($array, function ($name) use ($filter) {
            return false !== strpos($name, $filter);
        });
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templates = $this->getTemplates();

        $filter = $input->getArgument('filter');

        if ($filter) {
            $templates = $this->filterWith($templates, $filter);
        }

        if (empty($templates)) {
            $output->writeln("<error>No templates found</error>");
        } else {
            $output->writeln('Available Init Templates:');

            foreach ($templates as $package) {
                $output->writeln("  <info>{$package}</info>");
            }
        }
    }
}
