<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\Inflector;

class InflectorTest extends AbstractTestCase
{
    public function dataInitials()
    {
        return array(
            array('OpenBuildings', 'OB'),
            array('Clippings', 'CL'),
            array('Despark', 'DE'),
        );
    }

    /**
     * @dataProvider dataInitials
     * @covers CL\ComposerInit\Inflector::initials
     */
    public function testInitials($str, $expected)
    {
        $this->assertEquals($expected, Inflector::initials($str));
    }

    public function dataHumanize()
    {
        return array(
            array('test-title', 'test title'),
            array('test_title', 'test title'),
        );
    }

    /**
     * @dataProvider dataHumanize
     * @covers CL\ComposerInit\Inflector::humanize
     */
    public function testHumanize($str, $expected)
    {
        $this->assertEquals($expected, Inflector::humanize($str));
    }

    public function dataTitle()
    {
        return array(
            array('test-title', 'Test Title'),
            array('test_title', 'Test Title'),
        );
    }

    /**
     * @dataProvider dataTitle
     * @covers CL\ComposerInit\Inflector::title
     */
    public function testTitle($str, $expected)
    {
        $this->assertEquals($expected, Inflector::title($str));
    }

    public function dataTitlecase()
    {
        return array(
            array('test-title', 'TestTitle'),
            array('test_title', 'TestTitle'),
        );
    }

    /**
     * @dataProvider dataTitlecase
     * @covers CL\ComposerInit\Inflector::titlecase
     */
    public function testTitlecase($str, $expected)
    {
        $this->assertEquals($expected, Inflector::titlecase($str));
    }
}
