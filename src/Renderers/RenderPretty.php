<?php
namespace Gendiff\Renderers\RenderPretty;

use function Funct\Collection\flattenAll;

function renderPretty($tree, $lvl = 0): string
{
    $lines = array_map(function ($node) use ($lvl) {
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
                $str = insSpaces($lvl) . '    ' . $key . ': ' . renderPretty($children, $lvl + 1);
                break;
            case 'unchanged':
                $str = insSpaces($lvl) . '    ' . $key . ': ' . turnDataToStr($old, $lvl + 1);
                break;
            case 'changed':
                $str = [insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($old, $lvl + 1),
                insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($new, $lvl + 1)];
                break;
            case 'deleted':
                $str = insSpaces($lvl) . '  - ' . $key . ': ' . turnDataToStr($old, $lvl + 1);
                break;
            case 'added':
                $str = insSpaces($lvl) . '  + ' . $key . ': ' . turnDataToStr($new, $lvl + 1);
                break;
        }
        return $str;
    }, $tree);
    $text = implode(PHP_EOL, flattenAll($lines));
    return '{' . PHP_EOL . $text . PHP_EOL . insSpaces($lvl) . '}';
}

function turnDataToStr($data, $lvl = 0): string
{
    if (empty($data) || !is_array($data)) {
        return $data;
    }
    $keys = array_keys($data);
    $lines = array_reduce($keys, function ($carry, $key) use ($data, $lvl) {
        $carry[] = insSpaces($lvl + 1) . $key . ': ' . $data[$key];
        return $carry;
    }, []);
    $text = \implode(PHP_EOL, $lines) . PHP_EOL;
    return '{' . PHP_EOL . $text . insSpaces($lvl) . '}';
}

function insSpaces($lvl)
{
    return str_repeat(' ', $lvl * 4);
}
