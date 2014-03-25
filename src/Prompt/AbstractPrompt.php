<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\Param;
use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Inflector;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractPrompt
{
    abstract public function getName();
    abstract public function getDefaults(TemplateHelper $helper);

    public function getValues(OutputInterface $output, TemplateHelper $helper)
    {
        $dialog = $helper->getHelperSet()->get('dialog');
        $defaults = $this->getDefaults($helper);
        $firstDefault = reset($defaults);

        $title = Inflector::title($this->getName());

        $response = $dialog->ask(
            $output,
            "<info>{$title}</info> ($firstDefault): ",
            $firstDefault,
            $defaults
        );

        return $this->getValuesForResponse($response);
    }

    public function getValuesForResponse($response)
    {
        return array($this->getName() => $response);
    }
}
