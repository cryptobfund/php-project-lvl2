<?php

namespace Gendiff\Formatters\Plain;

use Exception;

function build($ast)
{
    return builder($ast);
}

function builder($ast, $level = "")
{
    $plain = array_reduce($ast, function ($acc, $item) use ($level) {
        try {
            $acc[] = getBlock($item, $level);
        } catch (Exception $e) {
            return "\n Program error. " . $e->getMessage() . "\n";
        }
        return $acc;
    });
    return implode("", $plain);
}

function getBlock($item, $level = "")
{
    $newLevel = strlen($level) > 0 ? "{$level}.{$item['key']}" : $item['key'];
    switch ($item['type']) {
        case 'added':
            return "Property '{$newLevel}' was added with value: '" . getValue($item['value']) . "'\n";
        case 'deleted':
            return "Property '{$newLevel}' was removed\n";
        case 'changed':
            return "Property '{$newLevel}' was changed." .
                " From '{$item['beforeValue']}' to '{$item['afterValue']}'\n";
        case 'parent':
            return builder($item['kids'], $newLevel) ;
        case 'unchanged':
            return '';
        default:
            throw new Exception("Unknown type: '{$item['type']}' of ast item: '{$item['key']}");
    }
}

function getValue($item)
{
    if (!is_array($item)) {
        return is_bool($item) ? getBoolToStr($item) : $item;
    } else {
        return "complex value";
    }
}

function getBoolToStr($item)
{
    return $item === true ? 'true' : 'false';
}
