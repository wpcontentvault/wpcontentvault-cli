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

use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use Illuminate\Support\Collection;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class DocumentObjectRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  Document  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): Collection
    {
        Document::assertInstanceOf($node);

        return $childRenderer->renderNodes($node->children());
    }

    public function getXmlTagName(Node $node): string
    {
        return 'document';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [
            'xmlns' => 'http://commonmark.org/xml/1.0',
        ];
    }
}
