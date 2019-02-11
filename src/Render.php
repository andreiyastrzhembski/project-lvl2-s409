<?php
namespace Gendiff\Render;

use function Gendiff\Renderers\RenderPretty\renderPretty;
use function Gendiff\Renderers\RenderPlain\renderPlain;

function render($tree, $format)
{
    switch ($format) {
        case 'pretty':
            return renderPretty($tree);
        case 'plain':
            return renderPlain($tree);
        case 'json':
            return \json_encode($tree);
        default:
            throw new \Exception("Unsupported format '{$format}'");
    }
}
