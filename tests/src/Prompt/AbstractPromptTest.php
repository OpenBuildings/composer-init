<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\Prompt\AbstractPrompt;
use CL\ComposerInit\TemplateHelper;
use Symfony\Component\Console\Application;

/**
 * @coversDefaultClass CL\ComposerInit\Prompt\AbstractPrompt
 */
class AbstractPromptTest extends AbstractTestCase
{
    /**
     * @covers ::getValuesForResponse
     */
    public function testGetValuesForResponse()
    {
        $prompt = $this->getMockForAbstractClass('CL\ComposerInit\Prompt\AbstractPrompt');

        $prompt
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test name'));

        $expected = array(
            'test name' => 'test response',
        );

        $this->assertEquals($expected, $prompt->getValuesForResponse('test response'));
    }

    public function testGetValues()
    {
        $template = new TemplateHelper();
        $application = new Application();
        $application->getHelperSet()->set($template);
        $template->setHelperSet($application->getHelperSet());
        $output = new DummyOutput();

        $prompt = $this->getMockForAbstractClass('CL\ComposerInit\Prompt\AbstractPrompt');

        $prompt
            ->expects($this->once())
            ->method('getDefaults')
            ->will($this->returnValue(array('default 1', 'default 2')));

        $prompt
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test name'));

        $prompt
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('Test Name!'));

        $template->getHelperSet()->get('dialog')->setInputStream($this->getInputStream("\n"));

        $values = $prompt->getValues($output, $template);

        $this->assertEquals("Test Name! (default 1): \n", $output->output);

        $expected = array(
            'test name' => 'default 1',
        );

        $this->assertEquals($expected, $values);
    }
}
