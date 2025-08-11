<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Models\Article;
use App\Models\Locale;
use App\Models\Tag;
use App\Registry\TagCategoriesRegistry;
use App\Repositories\TagRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use Illuminate\Support\Collection;

class ArticleTagger
{
    private ?Collection $tagsList = null;

    public function __construct(
        private TagRepository         $tags,
        private TagCategoriesRegistry $tagsRegistry,
        private ClassificationService $classificationService,
        private ManifestNameResolver  $manifestNameResolver,
        private ManifestUpdater       $manifestUpdater,
        private ApplicationOutput     $applicationOutput
    ) {}

    public function updateTagsForArticle(Article $article): void
    {
        if (null === $this->tagsList) {
            $this->tagsList = $this->tags->getAllTags()
                ->groupBy('category');
        }

        $tags = [];

        foreach ($this->tagsRegistry->categories as $categorySlug => $description) {
            if(false === $this->tagsList->has($categorySlug)) {
                throw new \RuntimeException("Tags for category {$categorySlug} not found");
            }
            $tagsInCategory = $this->tagsList->get($categorySlug);

            $tag = $this->suggestTagForArticleByTagCategory($article, $description, $tagsInCategory);

            if(null !== $tag) {
                $this->applicationOutput->info("Tag {$tag->slug} suggested for {$categorySlug}");
            }else{
                $this->applicationOutput->info("No tags suggested for {$categorySlug}");
            }

            if (null !== $tag) {
                $tags[] = $tag;
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

    public function suggestTagForArticleByTagCategory(Article $article, string $description, Collection $tags): ?Tag
    {
        return $this->classificationService->suggestTagForArticle($article, $tags, $description);
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
