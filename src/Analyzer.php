<?php

namespace Gendiff\Analyzer;

use function Gendiff\Parsers\parse;

function genDiff($beforeFilePath, $afterFilePath, $format = 'pretty')
{
    $beforeContent = file_get_contents($beforeFilePath);
    $afterContent = file_get_contents($afterFilePath);
    $beforeType = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
    $afterType = pathinfo($afterFilePath, PATHINFO_EXTENSION);
    $beforeParsedContent = parse($beforeContent, $beforeType);
    $afterParsedContent = parse($afterContent, $afterType);

    $formatter = chooseBuilder($format);
    return $formatter(astCreator($beforeParsedContent, $afterParsedContent));
}
function chooseBuilder($format)
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

function astCreator($beforeParsedContent, $afterParsedContent)
{
    $beforeKeys = array_keys($beforeParsedContent);
    $afterKeys = array_keys($afterParsedContent);
    $keys = array_unique(array_merge($beforeKeys, $afterKeys));
    return array_reduce($keys, function ($acc, $key) use ($beforeParsedContent, $afterParsedContent) {
        $acc[] = typeDef($key, $beforeParsedContent, $afterParsedContent);
        return $acc;
    });
}

function typeDef($key, $beforeParsedContent, $afterParsedContent)
{
    $beforeKey = $beforeParsedContent[$key];
    $afterKey = $afterParsedContent[$key];
    if (!array_key_exists($key, $beforeParsedContent)) {
        return ['type' => "added", 'key' => $key, 'value' => $afterKey];
    }
    if (!array_key_exists($key, $afterParsedContent)) {
        return ['type' => "deleted", 'key' => $key, 'value' => $beforeKey];
    }
    if ((is_array($beforeKey)) && (is_array($afterKey))) {
        return [
            'type' => "parent",'key' => $key,
            'children' => astCreator($beforeKey, $afterKey)];
    }
    if ($beforeKey !== $afterKey) {
        return [
            'type' => "changed",
            'key' => $key,
            'beforeValue' => $beforeKey,
            'afterValue' => $afterKey
        ];
    }
    return ['type' => "unchanged", 'key' => $key, 'value' => $beforeKey];
}
