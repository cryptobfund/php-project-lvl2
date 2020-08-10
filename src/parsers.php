<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($content, $type)
{
    switch ($type) {
        case "yaml":
            return Yaml::parse($content);
        case "json":
            return json_decode($content, true);
        default:
            throw new \Exception('Unknown type of file ' . $type);
    }
}
