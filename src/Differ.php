<?php
namespace Gendiff\Differ;

use function Funct\Collection\union;

use function Gendiff\Parser\getData;
use function Gendiff\Render\render;

function genDiff($pathToFile1, $pathToFile2, $format): string
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
    $tree = calcDiffTree($data1, $data2);
    return render($tree, $format);
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
                    $carry[] = createNode($key, 'unchanged', $oldValue, $newValue);
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

function createNode($key, $type, $oldValue, $newValue, $children = null)
{
    return [
        'key' => $key,
        'type' => $type,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}
