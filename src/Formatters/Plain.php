<?php

namespace Gendiff\Formatters\Plain;

function formatPlain($ast)
{
    return format($ast);
}

function format($ast, $level = "")
{
    $plain = array_map(function ($item) use ($level) {
        return getBlock($item, $level);
    }, $ast);
    return implode("\n", array_filter($plain, fn($item) => $item !== ''));
}

function getBlock($item, $level = "")
{
    $key = $item['key'];
    $newLevel = strlen($level) > 0 ? "{$level}.{$key}" : $key;
    switch ($item['type']) {
        case 'added':
            $value = formatValue($item['value']);
            return "Property '{$newLevel}' was added with value: '{$value}'";
        case 'deleted':
            return "Property '{$newLevel}' was removed";
        case 'changed':
            $beforeValue = formatValue($item['beforeValue']);
            $afterValue = formatValue($item['afterValue']);
            return "Property '{$newLevel}' was changed." .
                " From '{$beforeValue}' to '{$afterValue}'";
        case 'parent':
            return format($item['children'], $newLevel) ;
        case 'unchanged':
            return '';
        default:
            throw new \Exception("Unknown type: '{$item['type']}' of ast item: '{$key}");
    }
}

function formatValue($value)
{
    if (is_array($value)) {
        return "complex value";
    }
    return is_bool($value) ? getBoolToStr($value) : $value;
}

function getBoolToStr($value)
{
    return $value ? 'true' : 'false';
}
