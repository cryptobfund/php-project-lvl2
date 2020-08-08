<?php

namespace Gendiff\Analyzer;

use function Gendiff\Parsers\parse;

function genDiff($format, $beforeFilePath, $afterFilePath)
{
    //if (file_exists($beforeFilePath)) {
        $beforeContent = file_get_contents($beforeFilePath);
    //} else {
    //    $beforeContent = file_get_contents(__DIR__ . '/../' . $beforeFilePath);
    //}
    //if (file_exists($afterFilePath)) {
        $afterContent = file_get_contents($afterFilePath);
    //} else {
    //    $afterContent = file_get_contents(__DIR__ . '/../' . $afterFilePath);
    //}
    $dataType = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
    try {
        $beforeParsedContent = parse($beforeContent, $dataType);
        $afterParseContent = parse($afterContent, $dataType);
    } catch (\Exception $e) {
        echo "\n", "Program error. " , $e->getMessage(), "\n";
        exit;
    }

    $keysDeleted = array_diff_key($beforeParsedContent, $afterParseContent);
    $keysAdded = array_diff_key($afterParseContent, $beforeParsedContent);
    $keysUnchanged = array_intersect_assoc($beforeParsedContent, $afterParseContent);
    $keysMerge = array_merge($keysDeleted, $keysAdded, $keysUnchanged);
    $KeysChanged =
        array_merge_recursive(array_diff_key($beforeParsedContent, $keysMerge), array_diff_key($afterParseContent, $keysMerge));
    return builder($keysDeleted, $keysAdded, $keysUnchanged, $KeysChanged);
}

function builder($keysDeleted, $keysAdded, $keysUnchanged, $KeysChanged)
{
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
