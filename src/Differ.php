<?php
namespace Gendiff\Differ;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
    $diff = calcDiff($data1, $data2);

    $result = '{' . PHP_EOL;
    foreach ($diff as $key => $value) {
        $result .= '  ' . $key . ': ' . json_encode($value) . PHP_EOL;
    }
    $result .= '}' . PHP_EOL;

    return $result;
}

function getData(string $pathToFile): array
{
    $content = file_get_contents($pathToFile);
    return json_decode($content, true);
}

function calcDiff(array $data1, array $data2): array
{
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
    return $result;
}
