<?php

declare(strict_types=1);

namespace App\Services\Vault\Discovery;

use App\Models\Article;
use App\Services\Console\ApplicationOutput;

class ArticleContextDiscovery
{
    public function __construct(
        private ApplicationOutput $output
    ) {}

    public function discoverContext(Article $article): void
    {
        $contextFile = $article->path.'context.md';

        if (file_exists($contextFile)) {
            $contents = file_get_contents($contextFile);

            $article->context = $contents;
            $article->save();
        } else {
            $this->output->warning('Context file does not exist: '.$contextFile);
        }
    }
}
