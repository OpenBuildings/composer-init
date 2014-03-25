<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class AuthorName extends AbstractPrompt
{
    public function getName()
    {
        return 'author_name';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(
            $template->getGitConfig('user.name')
        );
    }
}
