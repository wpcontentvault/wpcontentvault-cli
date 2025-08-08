<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Str;
use RuntimeException;

class ResetArticleDirectoryNameCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-article-directory-name {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renames article directory in Vault';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

        if ($article->title === null || $article->external_id === null) {
            throw new RuntimeException('Article must be loaded to main site first!');
        }

        $name = $article->external_id . '. ' . $article->title;

        if (false === file_exists($article->path)) {
            throw new RuntimeException('Article directory does not exist!');
        }

        $basePath = dirname($article->path);

        $newPath = Str::finish($basePath, '/') . $name;

        rename($article->path, $newPath);

        $article->path = Str::finish($newPath, '/');
        $article->save();

        return self::SUCCESS;
    }
}
