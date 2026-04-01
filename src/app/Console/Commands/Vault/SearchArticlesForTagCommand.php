<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class SearchArticlesForTagCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-articles-for-tags';

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
        TagRepository     $tags,
        ManifestReader    $manifestReader,
        ManifestUpdater   $manifestUpdater
    ): int
    {
        $tagsList = $tags->getAllTags()->keyBy('id');

        $tagId = search(
            label: 'Choose tag',
            options: function (string $value) use ($tagsList) {
                if (strlen($value) > 0) {
                    return $tagsList->filter(function ($item) use ($value) {
                        return str_contains($item->slug, $value);
                    })->pluck('slug', 'id')->toArray();
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

        $selectedTag = $tagsList->get($tagId);

        $searchQuery = text(
            label: 'Enter search query',
            validate: function (string $query) {
                if (empty($query)) {
                    return 'Search query cannot be empty!';
                }

                return null;
            });

        $articlesList = $articles->searchArticles($searchQuery)->keyBy('id');

        $selected = multiselect(
            label: 'Choose articles for selected tag',
            options: $articlesList
                ->map(function (Article $article) {
                    $tags = implode(",", $article->tags->pluck('slug')->toArray());
                    $article->title = $article->title . "($tags)";

                    return $article;
                })
                ->pluck('title', 'id')->toArray()
        );

        foreach ($selected as $articleId) {
            $article = $articlesList->get($articleId);

            if (null === $article) {
                $this->error("Article '{$articleId}' not found!");

                continue;
            }

            $meta = $manifestReader->loadManifestFromPath($article->path, 'original');
            $tags = $meta->tags;
            $tags[] = $selectedTag;
            $manifestUpdater->updateTags($article->path, 'original', $tags);
        }

        return self::SUCCESS;
    }
}
