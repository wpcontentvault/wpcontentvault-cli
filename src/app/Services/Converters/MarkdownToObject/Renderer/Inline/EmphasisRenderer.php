<?php

declare(strict_types=1);

namespace App\Services\Converters\MarkdownToObject\Renderer\Inline;

use App\Blocks\Object\EmphasisObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class EmphasisRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        Emphasis::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        return new EmphasisObject($attrs,  $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'emph';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
