<?php
namespace Gendiff\Tests;

use \PHPUnit\Framework\TestCase;

use function Gendiff\Differ\genDiff;

class DifferTest extends TestCase
{
    private function getPath($filename)
    {
        return 'tests' . DIRECTORY_SEPARATOR
            . 'testData'  . DIRECTORY_SEPARATOR
            . $filename;
    }

    public function testGenDiffPretty()
    {
        $expected = file_get_contents($this->getPath('diff_string'));

        $actual = genDiff(
            $this->getPath('before.json'),
            $this->getPath('after.json'),
            'pretty'
        );
        $this->assertEquals($expected, $actual);

        $actual = genDiff(
            $this->getPath('before.yml'),
            $this->getPath('after.yml'),
            'pretty'
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffNestedPretty()
    {
        $expected = file_get_contents($this->getPath('diff_string_nested'));

        $actual = genDiff(
            $this->getPath('before_nested.json'),
            $this->getPath('after_nested.json'),
            'pretty'
        );
        $this->assertEquals($expected, $actual);

        $actual = genDiff(
            $this->getPath('before_nested.yml'),
            $this->getPath('after_nested.yml'),
            'pretty'
        );
        $this->assertEquals($expected, $actual);
    }
}
