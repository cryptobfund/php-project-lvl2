<?php

namespace Gendiff\Formatters\Pretty;

const SPACES_INIT_INDENT = 4;
const INDENTS_PER_TYPES = ['unchanged' => '    ', 'added' => '  + ', 'deleted' => '  - ', 'parent' => '    '];

function formatPretty($ast)
{
    return "{\n" . format($ast, 0) . "\n}";
}

function format($ast, $level)
{
    $pretty = array_map(function ($item) use ($level) {
        return getBlock($item, $level);
    }, $ast);
    return implode("\n", $pretty);
}

function getBlock($item, $level)
{
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
    $key = $item['key'];

    if ($item['type'] === 'parent') {
        $children = format($item['children'], $level + 1);
        return "{$spaces}    {$item['key']}: {\n{$children}\n    }";
    }

    if ($item['type'] === 'changed') {
        $beforeValue = formatValue($item['beforeValue'], $level + 1);
        $afterValue = formatValue($item['afterValue'], $level + 1);
        return "{$spaces}  - {$key}: {$beforeValue}\n" .
            "{$spaces}  + {$key}: {$afterValue}";
    }

    $value = formatValue($item['value'], $level + 1);
    $indent = INDENTS_PER_TYPES[$item['type']];
    return "{$spaces}{$indent}{$key}: {$value}";
}

function formatValue($value, $level = 1)
{
    if (is_array($value)) {
        $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
        $keys = array_keys($value);

        $result = array_map(function ($key) use ($level, $value, $spaces) {
            $formatValue = formatValue($value[$key], $level + 1);
            return "{$spaces}    {$key}: {$formatValue}";
        }, $keys);
        $result = implode("\n", $result);
        return "{\n{$result}\n{$spaces}}";
    }
    return is_bool($value) ? getBoolToStr($value) : $value;
}

function getBoolToStr($value)
{
    return $value ? 'true' : 'false';
}
