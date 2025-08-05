<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Repositories\ArticleRepository;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Str;
use RuntimeException;

class RenameArticleDirectoryCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename-article-directory {id} {name}';

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
        $name = $this->argument('name');

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

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
