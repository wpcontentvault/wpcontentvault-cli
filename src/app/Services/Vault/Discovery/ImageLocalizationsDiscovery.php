<?php

declare(strict_types=1);

namespace App\Services\Vault\Discovery;

use App\Models\Article;
use App\Models\Image;
use App\Models\ImageLocalization;
use App\Models\Locale;
use App\Repositories\LocaleRepository;
use App\Services\Database\Resolver\ImageDataResolver;

class ImageLocalizationsDiscovery
{
    public function __construct(
        private LocaleRepository  $locales,
        private ImageDataResolver $imageResolver,
    ) {}

    public function discoverImageLocalizations(Image $image, Article $article): void
    {
        $localesList = $this->locales->getAllLocales();

        foreach ($localesList as $locale) {
            $filePath = $article->path . $locale->code . '/' . $image->path;
            if (file_exists($filePath)) {
                $this->updateImageLocalization($filePath, $image, $locale);
            }
        }
    }

    private function updateImageLocalization(string $filePath, Image $image, Locale $locale): void
    {
        $localization = $image->findLocalizationByLocale($locale);

        if (null === $localization) {
            $localization = new ImageLocalization();
            $localization->article()->associate($image->article);
            $localization->image()->associate($image);
            $localization->locale()->associate($locale);
        }

        $imageData = $this->imageResolver->resolveImageOnSiteByLocale($filePath, $image->article->external_id, $locale);

        $localization->external_id = $imageData->externalId;
        $localization->external_url = $imageData->externalUrl;
        $localization->thumbnail_url = $imageData->thumbnailUrl;
        $localization->save();
    }
}
