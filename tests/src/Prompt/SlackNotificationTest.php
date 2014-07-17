<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\SlackNotification;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\SlackNotification
 */
class SlackNotificationTest extends AbstractTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $prompt = new SlackNotification();

        $this->assertEquals('slack_notification', $prompt->getName());
    }

    /**
     * @covers ::getDefaults
     */
    public function testGetDefaults()
    {
        $template = new TemplateHelper();

        $prompt = new SlackNotification();

        $expected = array(
            '',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }

    /**
     * @covers ::getValuesForResponse
     */
    public function testGetValuesForResponse()
    {
        $prompt = new SlackNotification();

        $expected = array(
            'slack_notification' => '',
        );

        $this->assertEquals($expected, $prompt->getValuesForResponse(''));

        $expected = array(
            'slack_notification' => "  slack:\n    secure: db1n+LD54Bo55IndiXJqAnlsIyrRFXGnYE/mS3gLtC/EQxPqfp9zsYvMl3IXuOCE9gc2lNjp/FJATVZMCnCLrP2uX+YlguX8r4+Qv89BQbKbk+q27NVlf+aXqWwK2gGhq1WdjOwqToejxt85518wzNC6FOy12WMsLgq/yT0vymY=\n",
        );

        $code = "db1n+LD54Bo55IndiXJqAnlsIyrRFXGnYE/mS3gLtC/EQxPqfp9zsYvMl3IXuOCE9gc2lNjp/FJATVZMCnCLrP2uX+YlguX8r4+Qv89BQbKbk+q27NVlf+aXqWwK2gGhq1WdjOwqToejxt85518wzNC6FOy12WMsLgq/yT0vymY=";

        $this->assertEquals($expected, $prompt->getValuesForResponse($code));

    }
}
