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

use App\Blocks\Object\HeadingObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class HeadingRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  Heading  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        Heading::assertInstanceOf($node);

        $level = $node->getLevel();

        $attrs = $node->data->get('attributes');
        $attrs['level'] = $level;

        return new HeadingObject(
            $attrs,
            $childRenderer->renderNodes($node->children())
        );
    }

    public function getXmlTagName(Node $node): string
    {
        return 'heading';
    }

    /**
     * @param  Heading  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Heading::assertInstanceOf($node);

        return ['level' => $node->getLevel()];
    }
}
