<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Inflector;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Title extends AbstractPrompt
{
    public function getName()
    {
        return 'title';
    }

    public function getTitle()
    {
        return 'Title';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(Inflector::title($template->getRepoField('name')));
    }

    public function getValuesForResponse($response)
    {
        return array(
            $this->getName() => $response,
            $this->getName().'_underline' => str_pad('', strlen($response), '='),
        );
    }
}
