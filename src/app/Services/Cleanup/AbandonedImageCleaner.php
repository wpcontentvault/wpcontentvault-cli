<?php

declare(strict_types=1);

namespace App\Services\Cleanup;

use App\Models\Article;
use App\Services\Console\ApplicationOutput;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class AbandonedImageCleaner
{
    public function __construct(
        private ApplicationOutput $output
    ) {}

    public function cleanAbandonedImages(Article $article): void
    {
        $images = $this->findAbandonedImagesForArticle($article);

        foreach ($images as $image) {
            $this->output->info("Deleting image {$image}");

            unlink($article->path . '/' . $image->getBasename());
        }
    }

    public function findAbandonedImagesForArticle(Article $article): Collection
    {
        $images = $article->images->keyBy('path');
        $notUsedImages = collect();

        $finder = new Finder;
        $finder->name(['*.jpeg', '*.jpg', '*.png', '*.gif', '*.mp4', '*.webm']);
        $finder->exclude(['cover']);
        $finder->sortByName();

        foreach ($finder->files()->in($article->path) as $imageFile) {
            if (false === $images->has($imageFile->getBasename())) {
                $notUsedImages->add($imageFile);
            }
        }

        return $notUsedImages;
    }
}
