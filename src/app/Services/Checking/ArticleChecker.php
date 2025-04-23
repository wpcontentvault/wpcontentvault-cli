<?php

declare(strict_types=1);

namespace App\Services\Checking;

use App\Blocks\ObjectBlock;
use App\Contracts\Checking\BlockCheckerInterface;
use App\Contracts\Checking\HasArticlePathInterface;
use App\Enum\BlockTypeEnum;
use App\Services\Checking\Checkers\Code\ContainsOnlyCyrillicChecker;
use App\Services\Checking\Checkers\Code\EmptyCodeBlockChecker;
use App\Services\Checking\Checkers\Image\HasNonExistentFileChecker;
use Illuminate\Support\Collection;

class ArticleChecker
{
    private array $checkers = [
        BlockTypeEnum::CODE->value => [
            EmptyCodeBlockChecker::class,
        ],
        BlockTypeEnum::IMAGE->value => [
            HasNonExistentFileChecker::class,
        ]
    ];

    public function __construct() {}

    public function registerChecker(string $type, string $checkerClass): void
    {
        if (false === isset($this->checkers[$type])) {
            $this->checkers[$type] = [];
        }

        $this->checkers[$type][] = $checkerClass;
    }

    public function checkArticleBlocks(Collection $articleBlocks, string $path): Collection
    {
        $errors = collect();

        $this->checkBlocks($articleBlocks, $path, $errors);

        return $errors;
    }

    private function checkBlocks(Collection $blocks, string $path, Collection $errors): void
    {
        foreach ($blocks as $block) {
            /** @var ObjectBlock $block */
            $this->checkBlock($block, $path, $errors);

            if (count($block->getChildren()) > 0) {
                $this->checkBlocks($block->getChildren(), $path, $errors);
            }
        }
    }

    private function checkBlock(ObjectBlock $block, string $path, Collection $errors): void
    {
        $checkers = $this->getCheckersForBlockType($block->getType());

        foreach ($checkers as $checkerClass) {
            $checker = new $checkerClass();

            if (false === $checker instanceof BlockCheckerInterface) {
                throw new \RuntimeException("Instance of BlockCheckerInterface expected, but got " . get_class($checker));
            }

            if ($checker instanceof HasArticlePathInterface) {
                $checker->setArticlePath($path);
            }

            $result = $checker->check($block);

            if ($result->failed) {
                $errors->add($result);
            }
        }
    }

    public function getCheckersForBlockType(string $type): array
    {
        if (false === isset($this->checkers[$type])) {
            return [];
        }

        return $this->checkers[$type];
    }
}
