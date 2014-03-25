<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Inflector;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PHPNamespace extends AbstractPrompt
{
    public function getName()
    {
        return 'php_namespace';
    }

    public function getDefaults(TemplateHelper $template)
    {
        list($vendor, $name) = explode('/', $template->getRepoField('full_name'));

        return array(
            Inflector::titlecase($vendor).'\\'.Inflector::titlecase($name),
            Inflector::initials($vendor).'\\'.Inflector::titlecase($name),
        );
    }

    public function getValuesForResponse($response)
    {
        return array(
            $this->getName() => $response,
            $this->getName().'_escaped' => addslashes($response),
        );
    }
}
