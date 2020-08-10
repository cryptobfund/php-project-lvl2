<?php

namespace Gendiff\Formaters\Plain;

function builderPlain($ast)
{
    return builder($ast);
}

function builder($ast, $level = "")
{
    $plain = array_reduce($ast, function ($acc, $item) use ($level) {
        $acc[] = getBlock($item, $level);
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
            echo " something wrong " . $item['type'];
            break;
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
