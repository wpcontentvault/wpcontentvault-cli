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

use App\Blocks\Object\ImageObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\StringContainerInterface;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class ImageRenderer implements NodeObjectRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param  Image  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeObjectRendererInterface $childRenderer): ObjectBlock
    {
        Image::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        $attrs['src'] = $node->getUrl();

        $attrs['alt'] = $this->getAltText($node);

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        return new ImageObject(
            $attrs,
            collect());
    }

    public function getXmlTagName(Node $node): string
    {
        return 'image';
    }

    /**
     * @param  Image  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Image::assertInstanceOf($node);

        return [
            'destination' => $node->getUrl(),
            'title' => $node->getTitle() ?? '',
        ];
    }

    private function getAltText(Image $node): string
    {
        $altText = '';

        foreach ((new NodeIterator($node)) as $n) {
            if ($n instanceof StringContainerInterface) {
                $altText .= $n->getLiteral();
            } elseif ($n instanceof Newline) {
                $altText .= "\n";
            }
        }

        return $altText;
    }
}
