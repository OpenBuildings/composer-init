<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use DirectoryIterator;
use GuzzleHttp\Client;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class UseCommand extends Command
{
    private $packegist;

    public function __construct(Client $packegist)
    {
        $this->packegist = $packegist;
    }

    public function getPackegist()
    {
        return $this->packegist;
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

    public function getDestination()
    {
        return '.';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('package');

        $distUrl = $this->getDistUrl($packageName);
        $zipFile = tempnam(sys_get_temp_dir(), 'package');

        $client = new Client();
        $client->get($distUrl, ['save_to' => $zipFile]);

        $templateDir = sys_get_temp_dir()."/composer-init-template/{$packageName}";
        mkdir($template, 0777, true);
        $this->deleteContants($templateDir);

        $zip = new ZipArchive();
        $zip->open($zipFile);
        $zip->extractTo($templateDir);
        $zip->close();

        $names = file_get_contents($templateDir.'/prompts.json', true);

        $output->writeln('Enter Template variables (Press enter for default):');

        $dialog = $this->getHelperSet()->get('dialog');

        $prompts = new Prompts();
        $values = $prompts->getValues($names, $output, $dialog);


        $valuesDisplay = "Use These Variables:\n";
        foreach ($values as $key => $value) {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }
        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment>:";

        if ($dialog->askConfirmation($output, $valuesDisplay, 'y') {
            $this->setTemplateVariables($templateDir.'root', $values);
            $this->moveFiles($templateDir.'/root', getcwd());
        } else {
            $output->writeln('<error>Aborted.</error>');
        }

        $this->deleteContants($templateDir);
        rmdir($templateDir);
        unlink($zipFile);

        $output->writeln('Done');
    }

    /**
     * @param string $from
     * @param string $to
     */
    public function moveFiles($from, $to)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $from,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $item) {
            $destination = $to.str_replace($from, '', $item->getPathname());

            if (! file_exists(dirname($destination))) {
                mkdir(dirname($destination));
            }

            rename($item->getPathname(), $destination);
        }
    }

    public function deleteContants($path)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $path,
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                unlink($item->getPathname());
            } else {
                rmdir($item->getPathname());
            }
        }
    }

    /**
     * @param string $packageName
     */
    public function getDistUrl($packageName)
    {
        $package = $this->packegist
            ->get("/packages/{$packageName}.json")
            ->json();

        return $package['package']['versions']['dev-master']['dist']['url'];
    }

    /**
     * @param string $directory
     * @param array  $values
     */
    public function setTemplateVariables($directory, array $values)
    {
        $templateVariables = array();
        foreach ($values as $key => $value) {
            $templateVariables["{%{$key}%}"] = $value;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($files as $file) {
            if ($file->isFile()) {
                $content = strtr(file_get_contents($file->getPathname()), $templateVariables);
                file_put_contents($file->getPathname(), $content);
            }
        }
    }
}
