<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Inflector;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Copyright extends AbstractPrompt
{
    public function getName()
    {
        return 'copyright';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(
            date('Y').', '.$template->getRepo()->getOrganizationName(),
            date('Y').', '.$template->getRepo()->getOwnerName(),
        );
    }

    public function getValuesForResponse($response)
    {
        return array(
            $this->getName() => $response,
            $this->getName().'_entity' => preg_replace('/^[\d\s\-,\.]+/', '', $response),
        );
    }

}
