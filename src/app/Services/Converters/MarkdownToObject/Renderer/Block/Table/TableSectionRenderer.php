<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services\Converters\MarkdownToObject\Renderer\Block\Table;

use App\Blocks\Object\Table\TableSectionBodyObject;
use App\Blocks\Object\Table\TableSectionHeadObject;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class TableSectionRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  TableSection  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer)
    {
        TableSection::assertInstanceOf($node);

        if (! $node->hasChildren()) {
            return '';
        }

        $attrs = $node->data->get('attributes');

        if ($node->getType() === TableSection::TYPE_HEAD) {
            return new TableSectionHeadObject($attrs, $childRenderer->renderNodes($node->children()));
        }

        return new TableSectionBodyObject($attrs, $childRenderer->renderNodes($node->children()));

    }

    public function getXmlTagName(Node $node): string
    {
        return 'table_section';
    }

    /**
     * @param  TableSection  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        TableSection::assertInstanceOf($node);

        return [
            'type' => $node->getType(),
        ];
    }
}
