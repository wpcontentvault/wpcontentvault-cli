<?php

declare(strict_types=1);

namespace App\Services\Converters\MarkdownToObject\Renderer;

use App\Blocks\Object\NewLineObject;
use App\Blocks\ObjectBlock;
use App\Contracts\CommonMark\ChildNodeObjectRendererInterface;
use App\Contracts\CommonMark\DocumentObjectRendererInterface;
use App\Contracts\CommonMark\NodeObjectRendererInterface;
use App\Services\Converters\MarkdownToObject\Renderer\Block\BlockQuoteRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\DocumentObjectRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\FencedCodeRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\HeadingRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\ListBlockRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\ListItemRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\ParagraphObjectRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\Table\TableCellRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\Table\TableRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\Table\TableRowRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Block\Table\TableSectionRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\EmphasisRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\ImageRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\LinkRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\NewlineRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\StrongRenderer;
use App\Services\Converters\MarkdownToObject\Renderer\Inline\TextRenderer;
use Illuminate\Support\Collection;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\NoMatchingRendererException;
use RuntimeException;

final class ObjectRenderer implements ChildNodeObjectRendererInterface, DocumentObjectRendererInterface
{
    private static array $overrides = [];

    private array $renderers = [
        Document::class => DocumentObjectRenderer::class,
        Paragraph::class => ParagraphObjectRenderer::class,
        Heading::class => HeadingRenderer::class,
        FencedCode::class => FencedCodeRenderer::class,
        //        Code::class => TextRenderer::class, //render inline code as regular text
        Newline::class => NewlineRenderer::class,
        Text::class => TextRenderer::class,
        Strong::class => StrongRenderer::class,
        Link::class => LinkRenderer::class,
        Image::class => ImageRenderer::class,
        ListBlock::class => ListBlockRenderer::class,
        ListItem::class => ListItemRenderer::class,
        Emphasis::class => EmphasisRenderer::class,
        Table::class => TableRenderer::class,
        TableCell::class => TableCellRenderer::class,
        TableRow::class => TableRowRenderer::class,
        TableSection::class => TableSectionRenderer::class,
        BlockQuote::class => BlockQuoteRenderer::class,
    ];

    public function __construct() {}

    public static function override($className, $rendererName): void
    {
        self::$overrides[$className] = $rendererName;
    }

    public function renderDocument(Document $document): Collection
    {
        $output = $this->renderNode($document);

        if ($output instanceof Collection === false) {
            throw new RuntimeException('Collection expected!');
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function renderNodes(iterable $nodes): Collection
    {
        $output = collect();

        $isFirstItem = true;

        foreach ($nodes as $node) {
            if (! $isFirstItem && $node instanceof AbstractBlock) {
                $output->add($this->getBlockSeparator());
            }

            $rendered = $this->renderNode($node);

            if ($rendered instanceof Collection) {
                $output = $output->merge($rendered);
            } else {
                $output->add($rendered);
            }

            $isFirstItem = false;
        }

        return $output;
    }

    public function getBlockSeparator(): ObjectBlock
    {
        return new NewLineObject([], collect(), "\n");
    }

    /**
     * @throws NoMatchingRendererException
     */
    private function renderNode(Node $node): ObjectBlock|Collection
    {
        $renderers = $this->getRenderersForClass(\get_class($node));

        foreach ($renderers as $renderer) {
            \assert($renderer instanceof NodeObjectRendererInterface);
            if (($result = $renderer->render($node, $this)) !== null) {
                return $result;
            }
        }

        throw new NoMatchingRendererException('Unable to find corresponding renderer for node type '.\get_class($node));
    }

    private function getRenderersForClass(string $class): array
    {
        if (isset(self::$overrides[$class])) {
            return [new self::$overrides[$class]];
        }

        $renderers = [];

        if (isset($this->renderers[$class])) {
            $renderers[] = new $this->renderers[$class];
        }

        return $renderers;
    }
}
