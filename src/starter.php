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

    $args = Docopt::handle($doc, array('version' => 'gendiff 0.1.0'));
    $format = (string) $args['--format'];
    $firstFilePath = $args['<firstFile>'];
    $secondFilePath = $args['<secondFile>'];

    print_r(genDiff($firstFilePath, $secondFilePath, $format));
    print_r("\n");
}
