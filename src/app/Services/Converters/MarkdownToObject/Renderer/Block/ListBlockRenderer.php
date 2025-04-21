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

use App\Blocks\Object\ListObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class ListBlockRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        /** @var ListBlock $node */
        ListBlock::assertInstanceOf($node);

        $listData = $node->getListData();

        $attrs = $node->data->get('attributes');

        if ($listData->start !== null && $listData->start !== 1) {
            $attrs['start'] = (string) $listData->start;
        }

        return new ListObject($attrs, $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'list';
    }

    /**
     * @param  ListBlock  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        ListBlock::assertInstanceOf($node);

        $data = $node->getListData();

        if ($data->type === ListBlock::TYPE_BULLET) {
            return [
                'type' => $data->type,
                'tight' => $node->isTight() ? 'true' : 'false',
            ];
        }

        return [
            'type' => $data->type,
            'start' => $data->start ?? 1,
            'tight' => $node->isTight(),
            'delimiter' => $data->delimiter ?? ListBlock::DELIM_PERIOD,
        ];
    }
}
