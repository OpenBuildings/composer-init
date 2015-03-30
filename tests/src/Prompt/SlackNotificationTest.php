<?php

namespace CL\ComposerInit\Test\Prompt;

use PHPUnit_Framework_TestCase;
use CL\ComposerInit\Prompt\SlackNotificationPrompt;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\SlackNotificationPrompt
 */
class SlackNotificationPromptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers getValues
     */
    public function testGetValues()
    {
        $prompt = new SlackNotificationPrompt();
        $output = new NullOutput();

        $dialog = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\DialogHelper')
            ->getMock();

        $dialog
            ->method('ask')
            ->with(
                $this->identicalTo($output),
                '<info>Encrypted Slack Notification code</info>: '
            )
            ->will($this->onConsecutiveCalls(null, 'NEW_NAME'));

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['slack_notification' => null], $values);

        $values = $prompt->getValues($output, $dialog);
        $this->assertEquals(['slack_notification' => "  slack:\n    secure: NEW_NAME\n"], $values);
    }
}
