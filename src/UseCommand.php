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

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class UseCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('use')
            ->setDescription('List available composer init templates')
            ->addArgument(
                'package',
                InputArgument::REQUIRED,
                'Package Name'
            )
            ->addArgument(
                'release',
                InputArgument::OPTIONAL,
                'The version to use, e.g. dev-master',
                'dev-master'
            );
    }

    public function getDestination()
    {
        return '.';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $template = $this->getHelperSet()->get('template');

        $distUrl = $this->getDistUrl($input->getArgument('package'), $input->getArgument('release'));


        $tempFile = tempnam(sys_get_temp_dir(), 'package');
        $template->download($output, $tempFile, $distUrl);

        $zip = new ZipArchive();
        $zip->open($tempFile);
        $zip->includeFile($zip->getRootDir().'Template.php');

        $output->writeln('Enter Template variables (Press enter for default):');
        $values = Template::getTemplateValues($output, $template);

        $output->writeln('');

        if ($template->confirmValues($output, $values)) {

            $zip->extractDirTo($zip->getRootDir().'root', $this->getDestination());
            $this->setTemplateVariables($this->getDestination().DIRECTORY_SEPARATOR.$zip->getRootDir().'root', $values);

            $this->moveFiles($this->getDestination().DIRECTORY_SEPARATOR.$zip->getRootDir().'root', $this->getDestination());
            $this->deleteDir($this->getDestination().DIRECTORY_SEPARATOR.$zip->getRootDir());

        } else {
            $output->writeln('<error>Aborted.</error>');
        }

        $zip->close();
        unlink($tempFile);

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

    public function deleteDir($path)
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

        rmdir($path);
    }

    /**
     * @param string $package_name
     * @param string $release
     */
    public function getDistUrl($package_name, $release)
    {
        $json = Curl::getJSON("https://packagist.org/packages/{$package_name}.json");

        return $json['package']['versions'][$release]['dist']['url'];
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
