<?php

declare(strict_types=1);

namespace App\Services\Converters\MarkdownToObject\Renderer\Block;

use App\Blocks\Object\CodeObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Util\Xml;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class FencedCodeRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  FencedCode  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        FencedCode::assertInstanceOf($node);

        $attrs = $node->data->getData('attributes');

        return new CodeObject(
            $attrs->export(),
            collect(),
            Xml::escape($node->getLiteral())
        );

    }

    public function getXmlTagName(Node $node): string
    {
        return 'code_block';
    }

    /**
     * @param  FencedCode  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        FencedCode::assertInstanceOf($node);

        if (($info = $node->getInfo()) === null || $info === '') {
            return [];
        }

        return ['info' => $info];
    }
}
