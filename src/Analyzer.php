<?php

namespace Gendiff\Analyzer;

use function Gendiff\Parsers\parse;

function readFile($filePath)
{
    $resultOfReading = file_get_contents($filePath);
    if (!$resultOfReading) {
        throw new \Exception("Can't read file: " . $filePath);
    }
    return $resultOfReading;
}

function genDiff($beforeFilePath, $afterFilePath, $format = 'pretty')
{
    $beforeContent = readFile($beforeFilePath);
    $afterContent = readFile($afterFilePath);
    $beforeType = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
    $afterType = pathinfo($afterFilePath, PATHINFO_EXTENSION);
    $beforeParsedContent = parse($beforeContent, $beforeType);
    $afterParsedContent = parse($afterContent, $afterType);

    $formatter = chooseFormatter($format);
    $ast = array_values(createAst($beforeParsedContent, $afterParsedContent));
    return $formatter($ast);
}
function chooseFormatter($format)
{

    switch ($format) {
        case "pretty":
            return 'Gendiff\Formatters\Pretty\formatPretty';
        case "plain":
            return 'Gendiff\Formatters\Plain\formatPlain';
        case "json":
            return 'Gendiff\Formatters\Json\formatJson';
        default:
            throw new \Exception('Unknown format: ' . $format);
    }
}

function createAst($beforeParsedContent, $afterParsedContent)
{
    $beforeKeys = array_keys($beforeParsedContent);
    $afterKeys = array_keys($afterParsedContent);
    $keys = array_unique(array_merge($beforeKeys, $afterKeys));

    return array_map(fn($key) => createItemOfAst($key, $beforeParsedContent, $afterParsedContent), $keys);
}

function createItemOfAst($key, $beforeParsedContent, $afterParsedContent)
{
    if (!array_key_exists($key, $beforeParsedContent)) {
        $afterValue = $afterParsedContent[$key];
        return ['type' => "added", 'key' => $key, 'value' => $afterValue];
    }
    if (!array_key_exists($key, $afterParsedContent)) {
        $beforeValue = $beforeParsedContent[$key];
        return ['type' => "deleted", 'key' => $key, 'value' => $beforeValue];
    }

    $beforeValue = $beforeParsedContent[$key];
    $afterValue = $afterParsedContent[$key];
    if (is_array($beforeValue) && is_array($afterValue)) {
        return [
            'type' => "parent",
            'key' => $key,
            'children' => array_values(createAst($beforeValue, $afterValue))];
    }
    if ($beforeValue !== $afterValue) {
        return [
            'type' => "changed",
            'key' => $key,
            'beforeValue' => $beforeValue,
            'afterValue' => $afterValue
        ];
    }
    return ['type' => "unchanged", 'key' => $key, 'value' => $beforeValue];
}
