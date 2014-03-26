<?php

namespace CL\ComposerInit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CL\ComposerInit\Packagist;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
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

            $zip->extractDirTo($zip->getRootDir().'root', '.');
            $this->setTemplateVariables($zip->getRootDir().'root', $values);

            $this->moveFiles($zip->getRootDir().'root', '.');
            rmdir($zip->getRootDir().'root');
            rmdir($zip->getRootDir());

        } else {
            $output->writeln('<error>Aborted.</error>');
        }

        $zip->close();
        unlink($tempFile);

        $output->writeln('Done');
    }

    public function moveFiles($from, $to)
    {
        $iterator = new DirectoryIterator($from);

        foreach ($iterator as $fileinfo) {
            if (! $fileinfo->isDot()) {
                rename($fileinfo->getPathname(), str_replace($from, $to, $fileinfo->getPathname()));
            }
        }
    }

    public function getDistUrl($package_name, $release)
    {
        $json = Curl::getJSON("https://packagist.org/packages/{$package_name}.json");

        return $json['package']['versions'][$release]['dist']['url'];
    }

    public function setTemplateVariables($directory, $values)
    {
        $templateVariables = array();
        foreach ($values as $key => $value) {
            $templateVariables["{%{$key}%}"] = $value;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($files as $file) {
            $content = strtr(file_get_contents($file->getPathname()), $templateVariables);
            file_put_contents($file->getPathname(), $content);
        }
    }
}
