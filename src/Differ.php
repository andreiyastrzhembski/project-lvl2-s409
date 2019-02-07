<?php
namespace Gendiff\Differ;

use Funct;
use function Gendiff\Parser\getData;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
    $changes = calcDiff($data1, $data2);
    $result = array_reduce(
        $changes,
        function ($carry, $item) {
            [$key, $status, $oldValue, $newValue] = $item;
            $oldValue = is_bool($oldValue) ? var_export($oldValue, true) : $oldValue;
            $newValue = is_bool($newValue) ? var_export($newValue, true) : $newValue;
            switch ($status) {
                case 'unchanged':
                    $str = '    ' . $key . ': ' . $oldValue . PHP_EOL;
                    break;
                case 'changed':
                    $str = '  - ' . $key . ': ' . $oldValue . PHP_EOL
                        . '  + ' . $key . ': ' . $newValue . PHP_EOL;
                    break;
                case 'deleted':
                    $str = '  - ' . $key . ': ' . $oldValue . PHP_EOL;
                    break;
                case 'added':
                    $str = '  + ' . $key . ': ' . $newValue . PHP_EOL;
                    break;
            }
            return $carry . $str;
        },
        ''
    );
    return '{' . PHP_EOL . $result . '}' . PHP_EOL;
}

function calcDiff(array $data1, array $data2): array
{
    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $allKeys = Funct\Collection\union($keys1, $keys2);

    $changes = array_map(function ($key) use ($data1, $data2) {
        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if ($data1[$key] === $data2[$key]) {
                $status = 'unchanged';
                $oldValue = $data1[$key];
                $newValue = $oldValue;
            } else {
                $status = 'changed';
                $oldValue = $data1[$key];
                $newValue = $data2[$key];
            }
        } elseif (array_key_exists($key, $data1)) {
            $status = 'deleted';
            $oldValue = $data1[$key];
            $newValue = null;
        } else {
            $status = 'added';
            $oldValue = null;
            $newValue = $data2[$key];
        }
        return [$key, $status, $oldValue, $newValue];
    }, $allKeys);
    return $changes;
}
