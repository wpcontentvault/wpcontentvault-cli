<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SearchArticlesForCategoryCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-articles-for-category';

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
        ArticleRepository  $articles,
        CategoryRepository $categories,
        ManifestUpdater    $manifestUpdater
    ): int
    {
        $categoriesList = $categories->getAllCategories()->keyBy('id');

        $categoryId = select(
            label: 'Choose category',
            options: $categoriesList->pluck('slug', 'id')->toArray(),
            validate: function (string $category) {
                if (empty($category)) {
                    return 'Category cannot be empty!';
                }

                return null;
            });

        $selectedCategory = $categoriesList->get($categoryId);

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
            options: $articlesList->pluck('title', 'id')->toArray()
        );

        foreach ($selected as $articleId) {
            $article = $articlesList->get($articleId);

            if (null === $article) {
                $this->error("Article '{$articleId}' not found!");

                continue;
            }

            $manifestUpdater->updateCategory($article->path, 'original', $selectedCategory);
        }

        return self::SUCCESS;
    }
}
