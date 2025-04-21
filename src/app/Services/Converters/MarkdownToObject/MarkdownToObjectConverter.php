<?php

declare(strict_types=1);

namespace App\Services\Converters\MarkdownToObject;

use App\Services\Converters\MarkdownToObject\Renderer\ObjectRenderer;
use Illuminate\Support\Collection;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Parser\MarkdownParser;

class MarkdownToObjectConverter
{
    public function convert(string $content): Collection
    {
        $environment = new Environment;
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new TableExtension);

        $parser = new MarkdownParser($environment);

        $result = $parser->parse($content);

        $renderer = new ObjectRenderer;

        return $renderer->renderDocument($result);
    }
}
