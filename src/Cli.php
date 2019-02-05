<?php
namespace Gendiff\Cli;

use function Gendiff\Differ\genDiff;

const DOC = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]

DOC;

function run()
{
    $args = \Docopt::handle(DOC);

    $pathToFile1 = $args['<firstFile>'];
    $pathToFile2 = $args['<secondFile>'];
    $diff = genDiff($pathToFile1, $pathToFile2);
    print_r($diff);
}
