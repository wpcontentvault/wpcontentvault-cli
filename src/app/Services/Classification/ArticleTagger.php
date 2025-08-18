<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Events\ArticleTagsUpdated;
use App\Models\Article;
use App\Models\Locale;
use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;

class ArticleTagger
{
    private ?Collection $tagsList = null;

    public function __construct(
        private TagRepository         $tags,
        private ClassificationService $classificationService,
        private ManifestNameResolver  $manifestNameResolver,
        private ManifestUpdater       $manifestUpdater,
        private ApplicationOutput     $applicationOutput,
        private Dispatcher            $dispatcher,
    ) {}

    public function updateTagsForArticle(Article $article): void
    {
        if (null === $this->tagsList) {
            $this->tagsList = $this->tags->getMatchableTags();
        }

        $suggestedTags = $this->suggestTagsForArticle(
            $article,
        );

        foreach ($article->localizations as $articleLocalization) {
            if (null === $articleLocalization) {
                continue;
            }

            $localizedTags = $this->getTagLocalizationsForLocale($suggestedTags, $articleLocalization->locale);

            $name = $this->manifestNameResolver->resolveName($article, $articleLocalization->locale);

            $this->manifestUpdater->updateTags($article->path, $name, $localizedTags);

            $this->dispatcher->dispatch(new ArticleTagsUpdated($article->external_id, $article->path, $name));
        }

    }

    public function suggestTagsForArticle(Article $article): Collection
    {
        $tagCategories = $this->tagsList->groupBy('category');
        $tagsCollection = $this->tagsList->keyBy('slug');

        $tagList = "";
        foreach ($tagCategories as $categorySlug => $tags) {
            $tagList .= $categorySlug . ":\n";
            foreach ($tags as $tag) {
                $tagList .= '- ' . $tag->slug . ' - ' . $tag->description . "\n";
            }
        }

        $suggestedCategories = $this->classificationService->suggestTagsForArticle($article, $tagList);

        $suggestedTags = [];

        foreach ($suggestedCategories as $tagCategory => $suggestedTagSlugs) {
            foreach ($suggestedTagSlugs as $suggestedTagSlug) {
                if (false === $tagsCollection->has($suggestedTagSlug)) {
                    $this->applicationOutput->warning("AI suggested not existent tag $suggestedTagSlug for $tagCategory");

                    continue;
                }
                $suggestedTags[] = $tagsCollection->get($suggestedTagSlug);

                $this->applicationOutput->info("Tag $suggestedTagSlug suggested for $tagCategory");
            }
        }

        return collect($suggestedTags);
    }

    private function getTagLocalizationsForLocale(Collection $tags, Locale $locale): Collection
    {
        $filtered = collect();

        foreach ($tags as $tag) {
            /** @var Tag $tag */
            $filtered->add($tag->findLocalizationByLocale($locale));
        }

        return $filtered;
    }
}
