<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Bugs extends AbstractPrompt
{
    public function getName()
    {
        return 'bugs';
    }

    public function getTitle()
    {
        return 'Issues url';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(
            $template->getRepoField('html_url').'/issues/new'
        );
    }
}
