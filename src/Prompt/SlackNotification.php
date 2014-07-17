<?php

namespace CL\ComposerInit\Prompt;

use CL\ComposerInit\TemplateHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class SlackNotification extends AbstractPrompt
{
    public function getName()
    {
        return 'slack_notification';
    }

    public function getDefaults(TemplateHelper $template)
    {
        return array(
            ''
        );
    }

    public function getTitle()
    {
        return 'Encrypted Slack Notification code';
    }

    public function getValuesForResponse($response)
    {
        return array(
            $this->getName() => $response ? "  slack:\n    secure: $response\n" : '',
        );
    }
}
