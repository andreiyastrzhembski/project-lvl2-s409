<?php
namespace Gendiff\Renderers\RenderPretty;

use function Funct\Collection\flattenAll;

function renderPretty($tree, $lvl = 0): string
{
    $result = array_map(function ($node) use ($lvl) {
        [
            'key' => $key,
            'type' => $type,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'children' => $children
        ] = $node;
        $old = is_bool($oldValue) ? var_export($oldValue, true) : $oldValue;
        $new = is_bool($newValue) ? var_export($newValue, true) : $newValue;
        switch ($type) {
            case 'nested':
                return insSpaces($lvl) . '    ' . $key . ': ' . renderPretty($children, $lvl + 1);
            case 'unchanged':
                return insSpaces($lvl) . '    ' . $key . ': ' . turnDataToStr($old, $lvl + 1);
            case 'changed':
                return [insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($old, $lvl + 1),
                insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($new, $lvl + 1)];
            case 'deleted':
                return insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($old, $lvl + 1);
            case 'added':
                return insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($new, $lvl + 1);
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
