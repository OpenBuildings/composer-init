<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use CL\ComposerInit\Prompt\PHPNamespace;

class PHPNamespaceTest extends AbstractTestCase
{
    /**
     * @covers CL\ComposerInit\Prompt\PHPNamespace::getName
     */
    public function testGetName()
    {
        $prompt = new PHPNamespace();

        $this->assertEquals('php_namespace', $prompt->getName());
    }

    /**
     * @covers CL\ComposerInit\Prompt\PHPNamespace::getDefaults
     */
    public function testGetDefaults()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField'));
        $template
            ->expects($this->once())
            ->method('getRepoField')
            ->with($this->equalTo('full_name'))
            ->will($this->returnValue('OpenBuildngs/test-repo'));

        $prompt = new PHPNamespace();

        $expected = array(
            'OpenBuildngs\TestRepo',
            'OB\TestRepo',
        );

        $this->assertEquals($expected, $prompt->getDefaults($template));
    }

    /**
     * @covers CL\ComposerInit\Prompt\PHPNamespace::getValuesForResponse
     */
    public function testGetValuesForResponse()
    {
        $prompt = new PHPNamespace();

        $expected = array(
            'php_namespace' => 'OpenBuildngs\TestRepo',
            'php_namespace_escaped' => 'OpenBuildngs\\\\TestRepo',
        );

        $this->assertEquals($expected, $prompt->getValuesForResponse('OpenBuildngs\TestRepo'));
    }
}
