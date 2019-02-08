<?php
namespace Gendiff\Tests;

use \PHPUnit\Framework\TestCase;

use function Gendiff\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('tests/testData/diff_string');

        $actual = genDiff('tests/testData/before.json', 'tests/testData/after.json');
        $this->assertEquals($expected, $actual);

        $actual = genDiff('tests/testData/before.yml', 'tests/testData/after.yml');
        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffNested()
    {
        $expected = file_get_contents('tests/testData/diff_string_nested');

        $actual = genDiff('tests/testData/before_nested.json', 'tests/testData/after_nested.json');
        $this->assertEquals($expected, $actual);

        $actual = genDiff('tests/testData/before_nested.yml', 'tests/testData/after_nested.yml');
        $this->assertEquals($expected, $actual);
    }
}
