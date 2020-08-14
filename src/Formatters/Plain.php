<?php

namespace Gendiff\Formatters\Plain;

function formatPlain($ast)
{
    return format($ast);
}

function format($ast, $level = "")
{
    $plain = array_reduce($ast, function ($acc, $item) use ($level) {
        $acc[] = getBlock($item, $level);
        return $acc;
    });
    return implode("\n", array_filter($plain, fn($item) => $item !== ''));
}

function getBlock($item, $level = "")
{
    $newLevel = strlen($level) > 0 ? "{$level}.{$item['key']}" : $item['key'];
    switch ($item['type']) {
        case 'added':
            $value = getValue($item['value']);
            return "Property '{$newLevel}' was added with value: '{$value}'";
        case 'deleted':
            return "Property '{$newLevel}' was removed";
        case 'changed':
            $beforeValue = getValue($item['beforeValue']);
            $afterValue = getValue($item['afterValue']);
            return "Property '{$newLevel}' was changed." .
                " From '{$beforeValue}' to '{$afterValue}'";
        case 'parent':
            return format($item['children'], $newLevel) ;
        case 'unchanged':
            return '';
        default:
            throw new \Exception("Unknown type: '{$item['type']}' of ast item: '{$item['key']}");
    }
}

function getValue($item)
{
    if (is_array($item)) {
        return "complex value";
    }
    return is_bool($item) ? getBoolToStr($item) : $item;
}

function getBoolToStr($item)
{
    return $item ? 'true' : 'false';
}
