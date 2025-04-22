<?php

declare(strict_types=1);

use App\Enum\BlockTypeEnum;

it('can parse simple table', function () {
    $content = <<<'STR'
| Header 1 | Header 2 |
|----------|----------|
| Cell 1   | Cell 2   |
| Cell 3   | Cell 4   |
STR;

    $converter = new \App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;

    $result = $converter->convert($content);

    $this->assertSame($result[0]->getType(), BlockTypeEnum::TABLE->value);
    $children = $result[0]->getChildren();

    // Check table structure
    $this->assertCount(3, $children); // Header row + 2 data rows

    // Check header row
    /** @var \App\Blocks\Object\Table\TableRowObject $headerRow */
    $tableHeader = $children[0];
    $this->assertSame(BlockTypeEnum::TABLE_SECTION_HEAD->value, $tableHeader->getType());
    $headerRow = $tableHeader->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TABLE_ROW->value, $headerRow->getType());

    $headerCells = $headerRow->getChildren();
    //Newline between two headers
    $this->assertCount(3, $headerCells);

    // Check header cells
    /** @var \App\Blocks\Object\Table\TableCellObject $headerCell1 */
    $headerCell1 = $headerCells[0];
    $this->assertSame(BlockTypeEnum::TABLE_HEADER_CELL->value, $headerCell1->getType());
    $this->assertSame('Header 1', $headerCell1->getChildren()[0]->getContent());

    $this->assertSame(BlockTypeEnum::NEWLINE->value, $headerCells[1]->getType());

    /** @var \App\Blocks\Object\Table\TableCellObject $headerCell2 */
    $headerCell2 = $headerCells[2];
    $this->assertSame(BlockTypeEnum::TABLE_HEADER_CELL->value, $headerCell2->getType());
    $this->assertSame('Header 2', $headerCell2->getChildren()[0]->getContent());

    // Check first data row
    /** @var \App\Blocks\Object\Table\TableRowObject $dataRow1 */
    $bodySection = $children[2];
    $this->assertSame(BlockTypeEnum::TABLE_SECTION_BODY->value, $bodySection->getType());
    $dataRow1 = $bodySection->getChildren()[0];
    $this->assertSame(BlockTypeEnum::TABLE_ROW->value, $dataRow1->getType());

    $dataCells1 = $dataRow1->getChildren();
    $this->assertCount(3, $dataCells1);

    // Check first row cells
    /** @var \App\Blocks\Object\Table\TableCellObject $cell1 */
    $cell1 = $dataCells1[0];
    $this->assertSame(BlockTypeEnum::TABLE_CELL->value, $cell1->getType());
    $this->assertSame('Cell 1', $cell1->getChildren()[0]->getContent());

    $this->assertSame(BlockTypeEnum::NEWLINE->value, $dataCells1[1]->getType());

    /** @var \App\Blocks\Object\Table\TableCellObject $cell2 */
    $cell2 = $dataCells1[2];
    $this->assertSame(BlockTypeEnum::TABLE_CELL->value, $cell2->getType());
    $this->assertSame('Cell 2', $cell2->getChildren()[0]->getContent());
});
