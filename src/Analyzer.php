<?php

namespace Gendiff\Analyzer;

use function Gendiff\Parsers\parse;

function genDiff($beforeFilePath, $afterFilePath, $format = 'pretty')
{
    $getParsed = fn($filePath) => parse(file_get_contents($filePath), pathinfo($filePath, PATHINFO_EXTENSION));
    $ast = astCreator($getParsed($beforeFilePath), $getParsed($afterFilePath));

    $builder = "Gendiff\Formatters\\" . ucfirst($format) . "\build";
    return $builder($ast);
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
        return ['type' => "added", 'key' => $key, 'value' => $afterParsedContent[$key]];
    }
    if (!array_key_exists($key, $afterParsedContent)) {
        return ['type' => "deleted", 'key' => $key, 'value' => $beforeParsedContent[$key]];
    }
    if ((is_array($beforeParsedContent[$key])) && (is_array($afterParsedContent[$key]))) {
        return [
            'type' => "parent",'key' => $key,
            'kids' => astCreator($beforeParsedContent[$key], $afterParsedContent[$key])];
    }
    if ($beforeParsedContent[$key] !== $afterParsedContent[$key]) {
        return [
            'type' => "changed",
            'key' => $key,
            'beforeValue' => $beforeParsedContent[$key],
            'afterValue' => $afterParsedContent[$key]
        ];
    }
    return ['type' => "unchanged", 'key' => $key, 'value' => $beforeParsedContent[$key]];
}
