<?php
namespace Gendiff\Differ;

use Funct;
use Symfony\Component\Yaml\Yaml;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
    $changes = calcDiff($data1, $data2);

    $result = array_reduce(
        $changes,
        function ($carry, $item) use ($data1, $data2) {
            [$key, $status] = $item;
            switch ($status) {
                case 'unchanged':
                    $str = '    ' . $key . ': ' . json_encode($data1[$key]) . PHP_EOL;
                    break;
                case 'changed':
                    $str = '  - ' . $key . ': ' . json_encode($data1[$key]) . PHP_EOL
                        . '  + ' . $key . ': ' . json_encode($data2[$key]) . PHP_EOL;
                    break;
                case 'deleted':
                    $str = '  - ' . $key . ': ' . json_encode($data1[$key]) . PHP_EOL;
                    break;
                case 'added':
                    $str = '  + ' . $key . ': ' . json_encode($data2[$key]) . PHP_EOL;
                    break;
            }

            return $carry . $str;
        },
        ''
    );

    return '{' . PHP_EOL . $result . '}' . PHP_EOL;
}

function readFile(string $pathToFile): string
{
    if (\is_readable($pathToFile)) {
        $content = file_get_contents($pathToFile);
    }
    return $content;
}

function getData(string $pathToFile): array
{
    $content = readFile($pathToFile);
    $parse = getParser($content);
    return $parse($content);
}

function getParser(string $content)
{
    switch ($content[0]) {
        case '{':
            $parse = function ($content) {
                return json_decode($content, true);
            };
            break;
        case '-':
            $parse = function ($content) {
                return Yaml::parse($content);
            };
            break;
    }
    return $parse;
}

function calcDiff(array $data1, array $data2): array
{
    /*
    $result = [];
    foreach ($data1 as $key => $value) {
        if (array_key_exists($key, $data2)) {
            if ($value === $data2[$key]) {
                $result['  ' . $key] = $value;
            } else {
                $result['- ' . $key] = $data1[$key];
                $result['+ ' . $key] = $data2[$key];
            }
        } else {
            $result['- ' . $key] = $value;
        }
    }
    foreach ($data2 as $key => $value) {
        if (!array_key_exists($key, $data1)) {
            $result['+ ' . $key] = $value;
        }
    }
    */

    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $allKeys = Funct\Collection\union($keys1, $keys2);

    $changes = array_map(function ($key) use ($data1, $data2) {
        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if ($data1[$key] === $data2[$key]) {
                $status = 'unchanged';
            } else {
                $status = 'changed';
            }
        } elseif (array_key_exists($key, $data1)) {
            $status = 'deleted';
        } else {
            $status = 'added';
        }
        return [$key, $status];
    }, $allKeys);

    return $changes;
}
