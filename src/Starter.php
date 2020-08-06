<?php

namespace Gendiff\Starter;

use Docopt;

use function Gendiff\Analyzer\genDiff;

function run()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]

DOC;

    $args = Docopt::handle($doc, array('version' => 'gendiff 0.0.1'));
    $format = $args['--format'];
    $firstFilePath = $args['<firstFile>'];
    $secondFilePath = $args['<secondFile>'];

    // foreach ($args as $k=>$v) {
    //     echo $k . ': ' . json_encode($v) . PHP_EOL;
    //}
    print_r(genDiff($format, $firstFilePath, $secondFilePath));
}
