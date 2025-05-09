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

namespace App\Services\Converters\MarkdownToObject\Renderer\Inline;

use App\Blocks\Object\NewLineObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class NewlineRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  Newline  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        Newline::assertInstanceOf($node);

        return new NewLineObject([], collect(), "\n");
    }

    /**
     * @param  Newline  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlTagName(Node $node): string
    {
        Newline::assertInstanceOf($node);

        return $node->getType() === Newline::SOFTBREAK ? 'softbreak' : 'linebreak';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
