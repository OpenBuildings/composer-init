<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\Inflector;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\ComposerInit\Inflector
 */
class InflectorTest extends PHPUnit_Framework_TestCase
{
    private $inflector;

    public function setUp()
    {
        parent::setUp();

        $this->inflector = new Inflector();
    }

    public function dataInitials()
    {
        return [
            ['OpenBuildings', 'OB'],
            ['Clippings', 'CL'],
            ['Despark', 'DE'],
        ];
    }

    /**
     * @dataProvider dataInitials
     * @covers ::initials
     */
    public function testInitials($str, $expected)
    {
        $this->assertEquals($expected, $this->inflector->initials($str));
    }

    public function dataHumanize()
    {
        return [
            ['test-title', 'test title'],
            ['test_title', 'test title'],
        ];
    }

    /**
     * @dataProvider dataHumanize
     * @covers ::humanize
     */
    public function testHumanize($str, $expected)
    {
        $this->assertEquals($expected, $this->inflector->humanize($str));
    }

    public function dataTitle()
    {
        return [
            ['test-title', 'Test Title'],
            ['test_title', 'Test Title'],
        ];
    }

    /**
     * @dataProvider dataTitle
     * @covers ::title
     */
    public function testTitle($str, $expected)
    {
        $this->assertEquals($expected, $this->inflector->title($str));
    }

    public function dataTitlecase()
    {
        return [
            ['test-title', 'TestTitle'],
            ['test_title', 'TestTitle'],
        ];
    }

    /**
     * @dataProvider dataTitlecase
     * @covers ::titlecase
     */
    public function testTitlecase($str, $expected)
    {
        $this->assertEquals($expected, $this->inflector->titlecase($str));
    }
}
