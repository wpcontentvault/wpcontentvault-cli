<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Models\Article;
use App\Models\Locale;
use App\Models\Tag;
use App\Registry\TagsRegistry;
use App\Repositories\TagCategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use Illuminate\Support\Collection;

class ArticleTagger
{
    private ?Collection $categoriesList = null;

    public function __construct(
        private TagCategoryRepository $categories,
        private ClassificationService $classificationService,
        private ManifestNameResolver  $manifestNameResolver,
        private ManifestUpdater       $manifestUpdater,
        private ApplicationOutput     $applicationOutput
    ) {}

    public function updateTagsForArticle(Article $article): void
    {
        if (null === $this->categoriesList) {
            $this->categoriesList = $this->categories->getAllTagCategories();
        }

        $tags = [];

        foreach ($this->categoriesList as $category) {
            $tagsInCategory = $category->tags;

            if(count($tagsInCategory) === 0) {
                throw new \RuntimeException("No tags in category $category->slug");
            }

            $suggestedTags = $this->suggestTagsForArticleByTagCategory(
                $article,
                $category->slug,
                $tagsInCategory
            );

            foreach($suggestedTags as $tag) {
                if (null !== $tag) {
                    $this->applicationOutput->info("Tag {$tag->slug} suggested for {$category->slug}");
                } else {
                    $this->applicationOutput->info("No tags suggested for {$category->slug}");
                }

                if (null !== $tag) {
                    $tags[] = $tag;
                }
            }
        }

        foreach ($article->localizations as $articleLocalization) {
            if (null === $articleLocalization) {
                continue;
            }

            $localizedTags = $this->getTagLocalizationsForLocale($tags, $articleLocalization->locale);

            $name = $this->manifestNameResolver->resolveName($article, $articleLocalization->locale);

            $this->manifestUpdater->updateTags($article->path, $name, $localizedTags);
        }
    }

    public function suggestTagsForArticleByTagCategory(Article $article, string $description, Collection $tags): Collection
    {
        return $this->classificationService->suggestTagsForArticle($article, $tags, $description);
    }

    private function getTagLocalizationsForLocale(array $tags, Locale $locale): Collection
    {
        $filtered = collect();

        foreach ($tags as $tag) {
            /** @var Tag $tag */
            $filtered->add($tag->findLocalizationByLocale($locale));
        }

        return $filtered;
    }
}
