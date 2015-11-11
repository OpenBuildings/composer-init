<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CL\ComposerInit\Prompt\Prompts;
use GuzzleHttp\Client;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class UseCommand extends Command
{
    /**
     * @var Client
     */
    private $packagist;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var Prompts
     */
    private $prompts;

    /**
     * @param Template $template
     * @param Prompts $prompts
     * @param Client $packagist
     */
    public function __construct(Template $template, Prompts $prompts, Client $packagist)
    {
        parent::__construct();

        $this->template = $template;
        $this->prompts = $prompts;
        $this->packagist = $packagist;
    }

    /**
     * @return Client
     */
    public function getPackegist()
    {
        return $this->packagist;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return Prompts
     */
    public function getPrompts()
    {
        return $this->prompts;
    }

    protected function configure()
    {
        $this
            ->setName('use')
            ->setDescription('List available composer init templates')
            ->addArgument(
                'package',
                InputArgument::REQUIRED,
                'Package Name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('package');
        $dialog = $this->getHelperSet()->get('dialog');

        $this->template->open($this->getPackageZipUrl($packageName));

        $output->writeln('Enter Template variables (Press enter for default):');

        $this->template->setValues(
            $this->prompts->getValues(
                $this->template->getPromptNames(),
                $output,
                $dialog
            )
        );

        $valuesDisplay = "Use These Variables:\n";

        foreach ($this->template->getValues() as $key => $value) {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }

        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment>:";

        if ($dialog->askConfirmation($output, $valuesDisplay, 'y')) {
            $this->template->putInto(getcwd());
            $output->writeln('Done');
        } else {
            $output->writeln('<error>Aborted</error>');
        }
    }

    /**
     * @param string $packageName
     */
    public function getPackageZipUrl($packageName)
    {
        $response = $this->packagist->get("/packages/{$packageName}.json");
        $package = json_decode($response->getBody(), true);

        return $package['package']['versions']['dev-master']['dist']['url'];
    }
}
