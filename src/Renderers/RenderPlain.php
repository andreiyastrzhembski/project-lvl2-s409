<?php
namespace Gendiff\Renderers\RenderPlain;

function renderPlain($tree): string
{
    $strings = turnDataToStrings($tree);
    return implode(PHP_EOL, $strings);
}

function turnDataToStrings($data, $parrents = []): array
{
    return array_reduce($data, function ($carry, $node) use ($parrents) {
        [
            'key' => $key,
            'type' => $type,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'children' => $children
        ] = $node;
        $old = is_bool($oldValue) ? var_export($oldValue, true) : $oldValue;
        $new = is_bool($newValue) ? var_export($newValue, true) : $newValue;
        $parrents[] = $key;
        $path = implode('.', $parrents);
        switch ($type) {
            case 'nested':
                return array_merge($carry, turnDataToStrings($children, $parrents));
            case 'changed':
                $carry[] = "Property '{$path}' was changed. From '{$old}' to '{$new}'";
                break;
            case 'deleted':
                $carry[] = "Property '{$path}' was removed";
                break;
            case 'added':
                if (is_array($newValue)) {
                    $carry[] = "Property '{$path}' was added with value: 'complex value'";
                } else {
                    $carry[] = "Property '{$path}' was added with value: '{$new}'";
                }
                break;
        }
        return $carry;
    }, []);
}
