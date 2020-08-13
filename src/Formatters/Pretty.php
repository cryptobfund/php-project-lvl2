<?php

namespace Gendiff\Formatters\Pretty;

const SPACES_INIT_INDENT = 4;

function build($ast)
{
    return "{\n" . builder($ast) . "}";
}

function builder($ast, $level = 0)
{
    $pretty = array_reduce($ast, function ($acc, $item) use ($level) {
        $acc[] = getBlock($item, $level);
        return $acc;
    });
    return implode("", $pretty);
}

function getBlock($item, $level = 0)
{
    $type = ['unchanged' => '    ', 'added' => '  + ', 'deleted' => '  - ', 'parent' => '    '];
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
    if ($item['type'] === 'parent') {
        return $spaces . "    {$item['key']}: {\n" . builder($item['kids'], $level + 1) . "    }\n";
    }
    if ($item['type'] === 'changed') {
        return $spaces . "  - {$item['key']}: {$item['beforeValue']}\n" .
            $spaces . "  + {$item['key']}: {$item['afterValue']}\n";
    }
    return $spaces . $type[$item['type']] . "{$item['key']}: " . getValue($item['value'], $level + 1) . "\n";
}

function getValue($item, $level = 1)
{
    if (!is_array($item)) {
        return is_bool($item) ? getBoolToStr($item) : $item;
    }
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
    $keys = array_keys($item);
    $result = array_reduce($keys, function ($acc, $key) use ($level, $item, $spaces) {
        $acc[] = $spaces . "    " . $key . ": " . getValue($item[$key], $level + 1) . "\n";
        return $acc;
    });
    return "{\n" . implode("", $result) . $spaces . "}";
}

function getBoolToStr($item)
{
    return $item === true ? 'true' : 'false';
}
