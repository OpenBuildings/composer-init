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
    public function getPackegist()
    {
        return $this->packagist;
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

    public function getTemplate($packageName)
    {
        return new Template($this->getPackageZipUrl($packageName));
    }

    public function getPrompts()
    {
        return new Prompts();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('package');
        $dialog = $this->getHelperSet()->get('dialog');

        $template = $this->getTemplate($packageName);
        $prompts = $this->getPrompts();

        $output->writeln('Enter Template variables (Press enter for default):');

        $template->setValues(
            $prompts->getValues(
                $template->getPromptNames(),
                $output,
                $dialog
            )
        );

        $valuesDisplay = "Use These Variables:\n";

        foreach ($template->getValues() as $key => $value) {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }

        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment>:";

        if ($dialog->askConfirmation($output, $valuesDisplay, 'y')) {
            $template->putInto(getcwd());
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
        $package = $this->packagist
            ->get("/packages/{$packageName}.json")
            ->json();

        return $package['package']['versions']['dev-master']['dist']['url'];
    }
}
