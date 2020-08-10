<?php

namespace Gendiff\Formaters\Plain;

function builderPlain($ast, $level = "")
{
    $result = "";
    foreach ($ast as $item) {
        $newLevel = strlen($level) > 0 ? "{$level}.{$item['key']}" : $item['key'];
        switch ($item['type']) {
            case 'added':
                $result .= "Property '{$newLevel}' was added with value: '" .
                    simpleBuilder($item['value']) . "'\n";
                break;
            case 'deleted':
                $result .= "Property '{$newLevel}' was removed\n";
                break;
            case 'changed':
                $result .= "Property '{$newLevel}' was changed." .
                    " From '{$item['beforeValue']}' to '{$item['afterValue']}'\n";
                break;
            case 'parent':
                $result .= builderPlain($item['kids'], $newLevel) ;
                break;
            case 'unchanged':
                break;
            default:
                echo " something wrong " . $item['type'];
                break;
        }
    }
    return $result;
}

function simpleBuilder($item)
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
