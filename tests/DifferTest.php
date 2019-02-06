<?php
namespace Gendiff\Tests;

use \PHPUnit\Framework\TestCase;

use function Gendiff\Differ\genDiff;
use function Gendiff\Differ\getData;
use function Gendiff\Differ\calcDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('tests/testData/diff_string_json');
        $actual = genDiff('tests/testData/before.json', 'tests/testData/after.json');
        $this->assertEquals($expected, $actual);
    }

    public function testCalcDiff()
    {
        $expected = getData('tests/testData/diff.json');
        $data1 = getData('tests/testData/before.json');
        $data2 = getData('tests/testData/after.json');
        $actual = calcDiff($data1, $data2);
        $this->assertEquals($expected, $actual);
    }
}
