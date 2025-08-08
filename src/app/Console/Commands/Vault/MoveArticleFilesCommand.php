<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Str;
use RuntimeException;

class MoveArticleFilesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move-article-directory {id} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves article directory in vault';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        VaultPathResolver $pathResolver,
    ): int
    {
        $id = intval($this->argument('id'));
        $path = Str::finish(Str::start($this->argument('path'), '/'), '/');

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

        if(false === file_exists($article->path)) {
            throw new RuntimeException('Article directory does not exist!');
        }

        $directoryName = basename($article->path);

        $newPath = $pathResolver->getArticlesRoot() . $path . $directoryName;

        rename($article->path, $newPath);

        $article->path = Str::finish($newPath, '/');
        $article->save();

        return self::SUCCESS;
    }
}
