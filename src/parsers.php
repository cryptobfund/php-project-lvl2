<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($content, $dateType)
{
    switch ($dateType) {
        case "yaml":
            return Yaml::parse($content);
        case "json":
            return json_decode($content, true);
        default:
            throw new \Exception('Wrong parse type of file');
    }
}
