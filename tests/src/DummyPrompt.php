<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\AbstractPrompt;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class DummyPrompt extends AbstractPrompt
{
    public function getName()
    {
        return 'dummy';
    }

    public function getTitle()
    {
        return 'Dummy';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array('dummy_default');
    }

    public function getValuesForResponse($response)
    {
        return array(
            $this->getName() => $response,
            $this->getName().'2' => $response.'2',
        );
    }

    public function getValues(OutputInterface $output, TemplateHelper $helper)
    {
        return $this->getValuesForResponse($this->getName());
    }
}
