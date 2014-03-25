<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Description extends AbstractPrompt
{
    public function getName()
    {
        return 'description';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(
            $template->getRepoField('description')
        );
    }
}
