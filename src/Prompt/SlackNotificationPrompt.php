<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class SlackNotificationPrompt implements PromptInterface
{
    public function getValues(OutputInterface $output, DialogHelper $dialog)
    {
        $value = $dialog->ask(
            $output,
            "<info>Encrypted Slack Notification code</info>: "
        );

        return [
            'slack_notification' => $value ? "  slack:\n    secure: $value\n" : null
        ];
    }
}

