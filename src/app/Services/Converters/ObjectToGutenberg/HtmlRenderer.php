<?php

declare(strict_types=1);

namespace App\Services\Converters\ObjectToGutenberg;

use App\Blocks\GutenbergBlock;
use App\Blocks\ObjectBlock;
use App\Contracts\Html\BlockHtmlRendererInterface;
use App\Contracts\Html\ChildBlockHtmlRendererInterface;
use App\Enum\BlockTypeEnum;
use App\Services\Converters\ObjectToGutenberg\Block\CodeRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\HeadingRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\ListBlockRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\ListItemRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\NewlineRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\ParagraphRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\QuoteRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\Table\TableRenderer;
use App\Services\Converters\ObjectToGutenberg\Block\VideoRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\EmphasisRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\ImageRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\LinkRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\StrongRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\Table\TableBodyRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\Table\TableCellRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\Table\TableHeaderCellRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\Table\TableHeadRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\Table\TableRowRenderer;
use App\Services\Converters\ObjectToGutenberg\Inline\TextRenderer;
use Illuminate\Support\Collection;
use League\CommonMark\Renderer\NoMatchingRendererException;

final class HtmlRenderer implements ChildBlockHtmlRendererInterface
{
    private static array $overrides = [];

    private array $renderers = [
        BlockTypeEnum::PARAGRAPH->value => ParagraphRenderer::class,
        BlockTypeEnum::HEADING->value => HeadingRenderer::class,
        BlockTypeEnum::TEXT->value => TextRenderer::class,
        BlockTypeEnum::EMPHASIS->value => EmphasisRenderer::class,
        BlockTypeEnum::IMAGE->value => ImageRenderer::class,
        BlockTypeEnum::CODE->value => CodeRenderer::class,
        BlockTypeEnum::QUOTE->value => QuoteRenderer::class,
        BlockTypeEnum::NEWLINE->value => NewlineRenderer::class,
        BlockTypeEnum::STRONG->value => StrongRenderer::class,
        BlockTypeEnum::LINK->value => LinkRenderer::class,
        BlockTypeEnum::VIDEO_LINK->value => VideoRenderer::class,
        BlockTypeEnum::LIST->value => ListBlockRenderer::class,
        BlockTypeEnum::LIST_ITEM->value => ListItemRenderer::class,
        BlockTypeEnum::TABLE->value => TableRenderer::class,
        BlockTypeEnum::TABLE_ROW->value => TableRowRenderer::class,
        BlockTypeEnum::TABLE_CELL->value => TableCellRenderer::class,
        BlockTypeEnum::TABLE_HEADER_CELL->value => TableHeaderCellRenderer::class,
        BlockTypeEnum::TABLE_SECTION_BODY->value => TableBodyRenderer::class,
        BlockTypeEnum::TABLE_SECTION_HEAD->value => TableHeadRenderer::class,
    ];

    public function __construct() {}

    public static function override(string $className, string $rendererName): void
    {
        self::$overrides[$className] = $rendererName;
    }

    public function renderRootNodes(iterable $nodes): Collection
    {
        $output = \collect();

        foreach ($nodes as $node) {
            $output->add($this->renderNode($node));
        }

        return $output;
    }

    public function renderNodes(iterable $nodes): string
    {
        $output = '';

        foreach ($nodes as $node) {
            $output .= $this->renderNode($node, true);
        }

        return $output;
    }

    public function renderNode(ObjectBlock $node, bool $inline = false): GutenbergBlock|string
    {
        if ($inline && $node->getType() === BlockTypeEnum::NEWLINE->value) {
            return "\n";
        }

        $renderers = $this->getRenderersForBlockType($node->getType());

        foreach ($renderers as $renderer) {
            \assert($renderer instanceof BlockHtmlRendererInterface);

            if (($result = $renderer->render($node, $this)) !== null) {
                return $result;
            }
        }

        throw new NoMatchingRendererException('Unable to find corresponding renderer for node type '.\get_class($node));
    }

    private function getRenderersForBlockType(string $type): array
    {
        if (isset(self::$overrides[$type])) {
            return [new self::$overrides[$type]];
        }

        $renderers = [];

        if (isset($this->renderers[$type])) {
            $renderers[] = new $this->renderers[$type];
        }

        return $renderers;
    }
}
