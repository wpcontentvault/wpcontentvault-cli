<?php

namespace App\Services\Checking;

use App\Context\Checking\CheckingResult;
use App\Models\Article;
use App\Services\Console\ApplicationOutput;
use App\Services\Database\Deserializer\ArticleBlocksDeserializer;
use Illuminate\Support\Collection;

class CheckingService
{
    public function __construct(
        private ArticleBlocksDeserializer $blocksDeserializer,
        private ArticleChecker            $checker,
        private ApplicationOutput         $output
    ) {}

    public function checkArticle(Article $article): int
    {
        $this->output->info("Checking article $article->title, ({$article->external_id})");

        $articleBlocks = $this->blocksDeserializer->deserialize($article);

        $errors = $this->checker->checkArticleBlocks($articleBlocks, $article->path);

        if (count($errors) === 0) {
            $this->output->info('No errors');

            return 0;
        }

        $this->printErrors($errors);

        return count($errors);
    }

    public function printErrors(Collection $errors): void
    {
        foreach ($errors as $error) {
            /** @var CheckingResult $error */
            $this->output->error("Found error {$error->message} in block {$error->block->getType()} with content {$error->block->getContent()}");
        }
    }
}
