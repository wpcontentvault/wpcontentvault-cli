<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Models\Article;
use App\Repositories\CategoryRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use Illuminate\Support\Collection;

class ArticleCategorizer
{
    private ?Collection $categoriesList = null;

    public function __construct(
        private CategoryRepository    $categories,
        private ClassificationService $classificationService,
        private ManifestNameResolver  $manifestNameResolver,
        private ManifestUpdater       $manifestUpdater,
        private ApplicationOutput     $applicationOutput
    ) {}

    public function updateCategoryForArticle(Article $article): void
    {
        if (null === $this->categoriesList) {
            $this->categoriesList = $this->categories->getAllCategories();
        }

        $category = $this->classificationService->suggestCategoryForArticle($article, $this->categoriesList);

        $this->applicationOutput->info("Category suggested: " . $category->slug);

        if (null == $category) {
            throw new \RuntimeException("Can't determine category for article {$article->external_id}");
        }

        foreach ($category->localizations as $localization) {
            $articleLocalization = $article->findLocalizationByLocale($localization->locale);

            if (null === $articleLocalization) {
                continue;
            }

            $name = $this->manifestNameResolver->resolveName($article, $articleLocalization->locale);

            $this->manifestUpdater->updateCategory($article->path, $name, $localization);
        }
    }
}
