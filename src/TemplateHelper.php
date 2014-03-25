<?php

namespace CL\ComposerInit;

use CL\ComposerInit\Prompt\AbstractPrompt;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class TemplateHelper extends Helper
{
    protected $repo;
    protected $organization;
    protected $owner;
    protected $application;

    public function setApplication(Application $application)
    {
        $this->application = $application;
        return $this;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getName()
    {
        return 'template';
    }

    public function retrieveParams(OutputInterface $output, array $params)
    {
        $values = array(
            'repository_name' => $this->getRepoField('full_name'),
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

        Curl::download($url, $file);

        $output->writeln("<info>Done.</info>");
    }

    public function confirmValues(OutputInterface $output, array $values)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $valuesDisplay = "Use These Variables:\n";
        foreach ($values as $key => $value)
        {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }
        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment>:";

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

    public function getGithub()
    {
        return $this->getApplication()->getGithub();
    }

    public function showGithubRepo($user, $name)
    {
        return $this->getGithub()->api('repo')->show($user, $name);
    }

    public function showGithubUser($login)
    {
        return $this->getGithub()->api('user')->show($login);
    }

    public function getRepo()
    {
        if ( ! $this->repo)
        {
            $origin = $this->getGitConfig('remote.origin.url');

            if ($origin)
            {
                preg_match('/^git@github.com:(.*)\/(.*).git$/', $origin, $matches);

                $this->repo = $this->showGithubRepo($matches[1], $matches[2]);
            }
        }

        return $this->repo;
    }

    public function getRepoField($field)
    {
        $repo = $this->getRepo();

        return isset($repo[$field]) ? $repo[$field] : null;
    }

    public function getOrganization()
    {
        if ( ! $this->organization)
        {
            if (($organization = $this->getRepoField('organization')))
            {
                $this->organization = $this->showGithubUser($organization['login']);
            }
        }

        return $this->organization;
    }

    public function getOwner()
    {
        if ( ! $this->owner)
        {
            if (($owner = $this->getRepoField('owner')))
            {
                $this->owner = $this->showGithubUser($owner['login']);
            }
        }

        return $this->owner;
    }
}
