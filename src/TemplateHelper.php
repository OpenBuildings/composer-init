<?php

namespace CL\ComposerInit;

use CL\ComposerInit\GitHub;
use CL\ComposerInit\Prompt\AbstractPrompt;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TemplateHelper extends Helper
{
    protected $repo;

    public function getName()
    {
        return 'template';
    }

    public function retrieveParams(OutputInterface $output, array $params)
    {
        $values = array(
            'repository_name' => $this->getRepo()->getFullName(),
        );

        foreach ($params as $param)
        {
            if (! ($param instanceof AbstractPrompt))
            {
                throw new InvalidArgumentException('All params must be instances of AbstractPrompt');
            }

            $values += $param->getValues($output, $this);
        }

        return $values;
    }

    public function download(OutputInterface $output, $file, $url)
    {
        $output->writeln("<info>Downloading:</info> $url");

        $bar = $this->getHelperSet()->get('progress');
        $bar->start($output, 1);

        Curl::download($url, $file, function($progress) use ($bar) {
            $bar->setCurrent($progress);
        });

        $bar->finish();
    }

    public function confirmValues(OutputInterface $output, array $values)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $valuesDisplay = "\nUse These Variables: \n";
        foreach ($values as $key => $value)
        {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }
        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment> ";

        return $dialog->askConfirmation(
            $output,
            $valuesDisplay,
            'y'
        );
    }

    public function getGitConfig($name)
    {
        return trim(`git config {$name}`);
    }

    public function getRepo()
    {
        if ( ! $this->repo)
        {
            $origin = $this->getGitConfig('remote.origin.url');

            if ($origin)
            {
                $this->repo = GitHub\Repo::newFromOrigin($origin);
            }
        }

        return $this->repo;
    }
}
