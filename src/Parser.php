<?php
namespace Gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function readFile(string $pathToFile): string
{
    if (\is_readable($pathToFile)) {
        $content = file_get_contents($pathToFile);
    } else {
        throw new \Exception("Cannot read file '{$pathToFile}'");
    }
    return $content;
}

function getType(string $pathToFile): string
{
    if (\is_readable($pathToFile)) {
        $type = pathinfo($pathToFile, PATHINFO_EXTENSION);
    } else {
        throw new \Exception("Cannot get file type '{$pathToFile}'");
    }
    return $type;
}

function getData(string $pathToFile): array
{
    $content = readFile($pathToFile);
    $type = getType($pathToFile);
    $parse = getParser($content, $type);
    return $parse($content);
}

function getParser(string $content, string $type)
{
    switch ($type) {
        case 'json':
            $parse = function ($content) {
                return json_decode($content, true);
            };
            break;
        case 'yaml':
        case 'yml':
            $parse = function ($content) {
                return Yaml::parse($content);
            };
            break;
        default:
            throw new \Exception("Unsupported data type '{$type}'");
            break;
    }
    return $parse;
}
