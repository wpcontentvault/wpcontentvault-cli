<?php

declare(strict_types=1);

namespace App\Services\Exporting\Mapper;

use App\Blocks\ObjectBlock;
use App\Enum\BlockTypeEnum;
use App\Models\Article;
use App\Models\Locale;
use App\Repositories\ImageRepository;
use App\Services\Database\Hasher\ImageHasher;
use Illuminate\Support\Collection;
use RuntimeException;

class ImageMapper
{
    public function __construct(
        private ImageRepository $images,
        private ImageHasher     $imageHasher,
    ) {}

    public function mapImagesToBlocks(Collection $blocks, Article $article, ?Locale $locale = null): void
    {
        $blocks->map(function (ObjectBlock $block) use ($article, $locale): void {
            if ($block->getType() === BlockTypeEnum::IMAGE->value) {
                $hash = $this->imageHasher->getHash($block);

                $image = $this->images->findImageByHashAndArticle($hash, $article);

                if ($image === null) {
                    throw new RuntimeException("Image {$block->getAttributes()['src']} not found!");
                }

                $block->addAttribute('external_id', strval($image->external_id));
                $block->addAttribute('external_url', $image->thumbnail_url);
                $block->addAttribute('file_url', $image->external_url);

                if (null !== $locale) {
                    $localization = $image->findLocalizationByLocale($locale);

                    if (null !== $localization) {
                        $block->addAttribute('external_id', strval($localization->external_id));
                        $block->addAttribute('external_url', $localization->thumbnail_url);
                        $block->addAttribute('file_url', $localization->external_url);
                    }
                }

            } else {
                $this->mapImagesToBlocks($block->getChildren(), $article, $locale);
            }
        });
    }
}
