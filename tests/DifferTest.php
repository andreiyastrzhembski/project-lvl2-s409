<?php
namespace Gendiff\Tests;

use \PHPUnit\Framework\TestCase;

use function Gendiff\Differ\getData;
use function Gendiff\Differ\getDiff;

class DifferTest extends TestCase
{
    public function testGetData()
    {
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22'
        ];
        $actual = getData('tests/testData/before.json');
        $this->assertEquals($expected, $actual);
    }

    public function testGetDiff()
    {
        $expected = [
            '  host' => 'hexlet.io',
            '+ timeout' => 20,
            '- timeout' => 50,
            '- proxy' => '123.234.53.22',
            '+ verbose' => true
        ];
        $data1 = getData('tests/testData/before.json');
        $data2 = getData('tests/testData/after.json');
        $actual = getDiff($data1, $data2);
        $this->assertEquals($expected, $actual);
    }
}
