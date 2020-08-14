<?php

namespace Gendiff\Formatters\Pretty;

const SPACES_INIT_INDENT = 4;
const TYPES = ['unchanged' => '    ', 'added' => '  + ', 'deleted' => '  - ', 'parent' => '    '];

function formatPretty($ast)
{
    return "{\n" . format($ast) . "\n}";
}

function format($ast, $level = 0)
{
    $pretty = array_reduce($ast, function ($acc, $item) use ($level) {
        $acc[] = getBlock($item, $level);
        return $acc;
    });
    return implode("\n", $pretty);
}

function getBlock($item, $level = 0)
{
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);

    if ($item['type'] === 'parent') {
        $children = format($item['children'], $level + 1);
        return "{$spaces}    {$item['key']}: {\n{$children}\n    }";
    }

    if ($item['type'] === 'changed') {
        $beforeValue = getValue($item['beforeValue'], $level + 1);
        $afterValue = getValue($item['afterValue'], $level + 1);
        return "{$spaces}  - {$item['key']}: {$beforeValue}\n" .
            "{$spaces}  + {$item['key']}: {$afterValue}";
    }

    $value = getValue($item['value'], $level + 1);
    $type = TYPES[$item['type']];
    return "{$spaces}{$type}{$item['key']}: {$value}";
}

function getValue($item, $level = 1)
{
    if (is_array($item)) {
        $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
        $keys = array_keys($item);
        $result = array_reduce($keys, function ($acc, $key) use ($level, $item, $spaces) {
            $value = getValue($item[$key], $level + 1);
            $acc[] = "{$spaces}    {$key}: {$value}";
            return $acc;
        });
        $result = implode("\n", $result);
        return "{\n{$result}\n{$spaces}}";
    }
    return is_bool($item) ? getBoolToStr($item) : $item;
}

function getBoolToStr($item)
{
    return $item ? 'true' : 'false';
}
