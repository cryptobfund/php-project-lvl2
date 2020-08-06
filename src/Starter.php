<?php

namespace Gendiff\Starter;
use Docopt;

function run()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version

DOC;

    $args = Docopt::handle($doc, array('version'=>'gendiff 0.0.1'));
    foreach ($args as $k=>$v) {
        echo $k . ': ' . json_encode($v) . PHP_EOL;
    }
}