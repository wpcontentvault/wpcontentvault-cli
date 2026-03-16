<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;
use App\Services\Vault\VaultPathResolver;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class SearchArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches articles for selected tag';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        VaultPathResolver $vaultPathResolver,
    ): int
    {
        $articleId = search(
            label: 'Type search query',
            options: function (string $value) use ($articles) {
                if (strlen($value) > 0) {
                    return $articles->searchArticles($value)->pluck('title', 'id')->toArray();
                } else {
                    return [];
                }
            },
            validate: function (string $tag) {
                if (empty($tag)) {
                    return 'Tag cannot be empty!';
                }

                return null;
            });

        $article = $articles->findArticleByUuid($articleId);
        $articlePath = $article->path;

        $root = $vaultPathResolver->getRoot();

        $articlePath = str_replace($root, '/home/extended/apps/vaultapp/vault/', $articlePath);
        $articlePath = urlencode($articlePath);

        $this->info("ID: " . $articleId);
        $this->info("External ID: " . $article->external_id);
        $this->info("Title: " . $article->title);
        $this->info("Published at: " . $article->published_at);
        $this->info("Modified at: " . $article->modified_at);
        $this->info("Author: " . $article->author);
        $this->info("Url: " . $article->url);
        $this->info("Original locale: " . $article->locale->code);
        $this->info("Path: file:///" . $articlePath);

        $additionalLocales = [];
        foreach ($article->localizations as $localization) {
            if ($localization->locale->code == $article->locale->code) {
                continue;
            }

            $additionalLocales[] = $localization->locale->code . " (" . $localization->external_id . ")";
        }

        $this->info("Additional locales: " . implode(', ', $additionalLocales));

        return self::SUCCESS;
    }
}
