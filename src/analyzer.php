<?php

namespace Gendiff\Analyzer;

use function Gendiff\Parsers\parse;
use function Gendiff\Formaters\Pretty\builder;

function genDiff($beforeFilePath, $afterFilePath)
{
    $beforeContent = file_get_contents($beforeFilePath);
    $afterContent = file_get_contents($afterFilePath);
    $type = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
    try {
        $beforeParsedContent = parse($beforeContent, $type);
        $afterParsedContent = parse($afterContent, $type);
    } catch (\Exception $e) {
        echo "\n", "Program error. ", $e->getMessage(), "\n";
        exit;
    }
    $tree = astCreator($beforeParsedContent, $afterParsedContent);
    return builder($tree);
}

function astCreator($beforeParsedContent, $afterParsedContent)
{
    $keys = array_keys(array_merge($beforeParsedContent, $afterParsedContent));
    return array_reduce($keys, function ($acc, $key) use ($beforeParsedContent, $afterParsedContent) {
        $acc[] = typeDef($key, $beforeParsedContent, $afterParsedContent);
        return $acc;
    });
}

function typeDef($key, $beforeParsedContent, $afterParsedContent)
{
    if (!array_key_exists($key, $beforeParsedContent)) {
        return [
            'type' => "added",
            'key' => $key,
            'value' => $afterParsedContent[$key]
        ];
    }
    if (!array_key_exists($key, $afterParsedContent)) {
        return [
            'type' => "deleted",
            'key' => $key,
            'value' => $beforeParsedContent[$key]
        ];
    }
    if ((is_array($beforeParsedContent[$key])) && (is_array($afterParsedContent[$key]))) {
        return [
            'type' => "parent",
            'key' => $key,
            'kids' => astCreator($beforeParsedContent[$key], $afterParsedContent[$key])
        ];
    }
    if ($beforeParsedContent[$key] !== $afterParsedContent[$key]) {
        return [
            'type' => "changed",
            'key' => $key,
            'beforeValue' => $beforeParsedContent[$key],
            'afterValue' => $afterParsedContent[$key]
        ];
    }
    if ($beforeParsedContent[$key] === $afterParsedContent[$key]) {
        return [
            'type' => "unchanged",
            'key' => $key,
            'value' => $beforeParsedContent[$key]
        ];
    }
}
