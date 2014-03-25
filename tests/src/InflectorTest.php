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
     */
    public function testInitials($str, $expected)
    {
        $this->assertEquals($expected, Inflector::initials($str));
    }
}
