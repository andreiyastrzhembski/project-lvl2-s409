<?php
namespace Gendiff\Differ;

use function Funct\Collection\union;
use function Funct\Collection\flattenAll;

use function Gendiff\Parser\getData;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
    $tree = calcDiffTree($data1, $data2);
    return turnDifftoText($tree);
}

function calcDiffTree(array $data1, array $data2): array
{
    $uniqKeys = union(array_keys($data1), array_keys($data2));
    return array_reduce($uniqKeys, function ($carry, $key) use ($data1, $data2) {
        $oldValue = isset($data1[$key]) ? $data1[$key] : null;
        $newValue = isset($data2[$key]) ? $data2[$key] : null;
        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if (is_array($oldValue) && is_array($newValue)) {
                $carry[] = createNode($key, 'nested', $oldValue, $newValue, calcDiffTree($oldValue, $newValue));
            } elseif (is_array($oldValue)) {
                $carry[] = createNode($key, 'unchanged', $oldValue, $newValue);
            } else {
                if ($oldValue == $newValue) {
                    $carry[] = createNode($key, 'unchanged', $oldValue, null);
                } else {
                    $carry[] = createNode($key, 'changed', $oldValue, $newValue);
                }
            }
        } else {
            if (array_key_exists($key, $data1)) {
                $carry[] = createNode($key, 'deleted', $oldValue, null);
            } else {
                $carry[] = createNode($key, 'added', null, $newValue);
            }
        }
        return $carry;
    }, []);
}

function createNode($key, $status, $oldValue, $newValue, $children = null)
{
    $old = is_bool($oldValue) ? var_export($oldValue, true) : $oldValue;
    $new = is_bool($newValue) ? var_export($newValue, true) : $newValue;
    return [
        'key' => $key,
        'status' => $status,
        'oldValue' => $old,
        'newValue' => $new,
        'children' => $children
    ];
}

function turnDifftoText($tree, $lvl = 0): string
{
    $result = array_map(function ($node) use ($lvl) {
        [
            'key' => $key,
            'status' => $status,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'children' => $children
        ] = $node;
        switch ($status) {
            case 'nested':
                return insSpaces($lvl) . '    ' . $key . ': ' . turnDifftoText($children, $lvl + 1);
            case 'unchanged':
                return insSpaces($lvl) . '    ' . $key . ': ' . turnDataToStr($oldValue, $lvl + 1);
            case 'changed':
                return [insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($oldValue, $lvl + 1),
                insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($newValue, $lvl + 1)];
            case 'deleted':
                return insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($oldValue, $lvl + 1);
            case 'added':
                return insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($newValue, $lvl + 1);
        }
    }, $tree);
    $text = implode(PHP_EOL, flattenAll($result));
    return '{' . PHP_EOL . $text . PHP_EOL . insSpaces($lvl) . '}';
}

function turnDataToStr($data, $lvl = 0): string
{
    if (empty($data) || !is_array($data)) {
        return $data;
    }
    $keys = array_keys($data);
    $strings = array_reduce($keys, function ($carry, $key) use ($data, $lvl) {
        $carry[] = insSpaces($lvl + 1) . $key . ': ' . $data[$key];
        return $carry;
    }, []);
    $str = \implode(PHP_EOL, $strings) . PHP_EOL;
    return '{' . PHP_EOL . $str . insSpaces($lvl) . '}';
}

function insSpaces($lvl)
{
    return str_repeat(' ', $lvl * 4);
}
