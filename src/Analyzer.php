<?php

namespace Gendiff\Analyzer;

function genDiff($format, $firstFilePath, $secondFilePath)
{
    if (file_exists($firstFilePath)) {
        $firstContent = json_decode(file_get_contents($firstFilePath), true);
    } else {
        $firstContent = json_decode(file_get_contents(__DIR__ . '/../'), true);
    }
    if (file_exists($secondFilePath)) {
        $secondContent = json_decode(file_get_contents($secondFilePath), true);
    } else {
        $secondContent = json_decode(file_get_contents(__DIR__ . '/../'), true);
    }

    $keysDeleted = array_diff_key($firstContent, $secondContent);
    $keysAdded = array_diff_key($secondContent, $firstContent);
    $keysUnchanged = array_intersect_assoc($firstContent, $secondContent);
    $keysMerge = array_merge($keysDeleted, $keysAdded, $keysUnchanged);
    $KeysChanged =
        array_merge_recursive(array_diff_key($firstContent, $keysMerge), array_diff_key($secondContent, $keysMerge));

    $result = "{\n";
    foreach ($keysUnchanged as $key => $value) {
        $result = $result . "    {$key}: {$value}\n";
    }
    foreach ($keysDeleted as $key => $value) {
        $result = $result . "  - {$key}: {$value}\n";
    }
    foreach ($keysAdded as $key => $value) {
        $result = $result . "  + {$key}: {$value}\n";
    }
    foreach ($KeysChanged as $key => $value) {
        $result = $result . "  - {$key}: {$value[0]}\n  + {$key}: {$value[1]}\n";
    }
    return $result . "}";
}
