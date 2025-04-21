<?php

declare(strict_types=1);

namespace App\Services\Converters\MarkdownToObject\Renderer\Block;

use App\Blocks\Object\QuoteObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

class BlockQuoteRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  BlockQuote  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        BlockQuote::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        $children = $childRenderer->renderNodes($node->children());

        return new QuoteObject($attrs, $children);
    }

    public function getXmlTagName(Node $node): string
    {
        return 'block_quote';
    }

    /**
     * @param  BlockQuote  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
