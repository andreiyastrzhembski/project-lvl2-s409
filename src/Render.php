<?php
namespace Gendiff\Render;

use function Gendiff\Renderers\RenderPretty\renderPretty;

function render($tree, $format)
{
    switch ($format) {
        case 'pretty':
            return renderPretty($tree);
        //case 'plain':
        default:
            throw new \Exception("Unknown format '{$format}'");
    }
}
