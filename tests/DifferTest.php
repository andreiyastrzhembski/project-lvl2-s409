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

        $actual = genDiff('tests/testData/before.yml', 'tests/testData/after.yml');
        $this->assertEquals($expected, $actual);
    }
}
