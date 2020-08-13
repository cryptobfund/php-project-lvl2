<?php

namespace Gendiff\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse($content, $type)
{
    switch ($type) {
        case "yaml":
        case "yml":
            return Yaml::parse($content);
        case "json":
            return json_decode($content, true);
        default:
            throw new Exception('Unknown type of file ' . $type);
    }
}
