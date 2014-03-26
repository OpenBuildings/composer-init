<?php

namespace CL\ComposerInit;

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
    protected $organization;
    protected $owner;
    protected $application;

    /**
     * @param  ComposerInitApplication $application
     * @return TemplateHelper          $this
     */
    public function setApplication(ComposerInitApplication $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return ComposerInitApplication
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template';
    }

    /**
     * @param  OutputInterface $output
     * @param  array           $params
     * @return array
     */
    public function retrieveParams(OutputInterface $output, array $params)
    {
        $values = array(
            'repository_name' => $this->getRepoField('full_name'),
        );

        foreach ($params as $param) {
            if (! ($param instanceof AbstractPrompt)) {
                throw new InvalidArgumentException('All params must be instances of AbstractPrompt');
            }

            $values += $param->getValues($output, $this);
        }

        return $values;
    }

    /**
     * @param  OutputInterface $output
     * @param  string          $file
     * @param  string          $url
     * @return void
     */
    public function download(OutputInterface $output, $file, $url)
    {
        $output->writeln("<info>Downloading:</info> $url");

        Curl::download($url, $file);

        $output->writeln("<info>Done.</info>");
    }

    /**
     * @param  OutputInterface $output [description]
     * @param  array           $values [description]
     * @return void
     */
    public function confirmValues(OutputInterface $output, array $values)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $valuesDisplay = "Use These Variables:\n";
        foreach ($values as $key => $value) {
            $valuesDisplay .= "  <info>$key</info>: $value\n";
        }
        $valuesDisplay .= "Confirm? <comment>(Y/n)</comment>:";

        return $dialog->askConfirmation(
            $output,
            $valuesDisplay,
            'y'
        );
    }

    /**
     * @param string $name
     */
    public function getGitConfig($name)
    {
        return trim(`git config {$name}`);
    }

    /**
     * @return \Github\Client
     */
    public function getGithub()
    {
        return $this->getApplication()->getGithub();
    }

    /**
     * @param  string $name
     * @param  string $user
     * @return array
     */
    public function showGithubRepo($user, $name)
    {
        return $this->getGithub()->api('repo')->show($user, $name);
    }

    /**
     * @param  string $login
     * @return array
     */
    public function showGithubUser($login)
    {
        return $this->getGithub()->api('user')->show($login);
    }

    /**
     * @return array
     */
    public function getRepo()
    {
        if (! $this->repo) {
            $origin = $this->getGitConfig('remote.origin.url');

            if ($origin) {
                preg_match('/^.*github.com:(.*)\/(.*).git$/', $origin, $matches);

                $this->repo = $this->showGithubRepo($matches[1], $matches[2]);
            }
        }

        return $this->repo;
    }

    /**
     * @param  string $field
     * @return string
     */
    public function getRepoField($field)
    {
        $repo = $this->getRepo();

        return isset($repo[$field]) ? $repo[$field] : null;
    }

    /**
     * @return array
     */
    public function getOrganization()
    {
        if (! $this->organization) {
            if (($organization = $this->getRepoField('organization'))) {
                $this->organization = $this->showGithubUser($organization['login']);
            }
        }

        return $this->organization;
    }

    /**
     * @return array
     */
    public function getOwner()
    {
        if (! $this->owner) {
            if (($owner = $this->getRepoField('owner'))) {
                $this->owner = $this->showGithubUser($owner['login']);
            }
        }

        return $this->owner;
    }
}
