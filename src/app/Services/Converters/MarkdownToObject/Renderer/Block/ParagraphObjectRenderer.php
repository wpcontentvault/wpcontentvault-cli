<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\Converters\MarkdownToObject\Renderer\Block;

use App\Blocks\Object\ParagraphObject;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Block\TightBlockInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class ParagraphObjectRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  Paragraph  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer)
    {
        Paragraph::assertInstanceOf($node);

        if ($this->inTightList($node)) {
            return $childRenderer->renderNodes($node->children());
        }

        $attrs = $node->data->get('attributes');

        return new ParagraphObject(
            $attrs, $childRenderer->renderNodes($node->children())
        );
    }

    public function getXmlTagName(Node $node): string
    {
        return 'paragraph';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }

    private function inTightList(Paragraph $node): bool
    {
        // Only check up to two (2) levels above this for tightness
        $i = 2;
        while (($node = $node->parent()) && $i--) {
            if ($node instanceof TightBlockInterface) {
                return $node->isTight();
            }
        }

        return false;
    }
}
