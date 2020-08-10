<?php

namespace Gendiff\Formaters\Pretty;

const SPACES_INIT_INDENT = 4;

function builderPretty($ast, $level = 0)
{
    $result = "{\n";
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
    foreach ($ast as $item) {
        switch ($item['type']) {
            case 'unchanged':
                $result .= $spaces . "    {$item['key']}: " . simpleBuilder($item['value'], $level + 1) . "\n";
                break;
            case 'added':
                $result .= $spaces . "  + {$item['key']}: " . simpleBuilder($item['value'], $level + 1) . "\n";
                break;
            case 'deleted':
                $result .= $spaces . "  - {$item['key']}: " . simpleBuilder($item['value'], $level + 1) . "\n";
                break;
            case 'changed':
                $result .= $spaces . "  - {$item['key']}: {$item['beforeValue']}\n" .
                    $spaces . "  + {$item['key']}: {$item['afterValue']}\n";
                break;
            case 'parent':
                $result .= $spaces . "    {$item['key']}: " . builderPretty($item['kids'], $level + 1) . "\n";
                break;
            default:
                echo "something wrong " . $item['type'];
                break;
        }
    }
    return $result . $spaces . "}";
}

function simpleBuilder($item, $level = 1)
{
    if (!is_array($item)) {
        return is_bool($item) ? getBoolToStr($item) : $item;
    }
    $spaces = str_repeat(" ", $level * SPACES_INIT_INDENT);
    $result = "{\n";
    foreach ($item as $key => $value) {
        if (is_array($value)) {
            $result = $result . $spaces . "    {$key}: " . simpleBuilder($value, $level + 1);
        } else {
            $result = $result . $spaces . "    {$key}: {$value}\n";
        }
    }
    return $result . $spaces . "}";
}

function getBoolToStr($item)
{
    return $item === true ? 'true' : 'false';
}
