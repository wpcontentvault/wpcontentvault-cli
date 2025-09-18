<?php

declare(strict_types=1);

namespace App\Services\Database\Serializer;

use App\Blocks\ObjectBlock;
use App\Configuration\GlobalConfiguration;
use App\Enum\BlockTypeEnum;
use App\Models\Article;
use App\Repositories\ImageRepository;
use App\Services\Database\Hasher\ImageHasher;
use App\Services\Database\Resolver\ImageDataResolver;
use App\Services\Vault\Discovery\ImageLocalizationsDiscovery;
use App\Services\Wordpress\ImageUpdater;
use Illuminate\Support\Str;

class ImageSerializer
{
    public function __construct(
        private ImageRepository             $images,
        private ImageHasher                 $imageHasher,
        private ImageDataResolver           $imageResolver,
        private ImageUpdater                $imageUpdater,
        private GlobalConfiguration         $config,
        private ImageLocalizationsDiscovery $imageDiscovery,
    ) {}

    public function serializeImage(ObjectBlock $block, Article $article)
    {
        \assert($block->getType() === BlockTypeEnum::IMAGE->value);

        $hash = $this->imageHasher->getHash($block);

        $image = $this->images->findImageByHashAndArticle($hash, $article);

        $imagePath = Str::finish($article->path, '/') . $block->getAttributes()['src'];

        if ($image === null) {
            $uploaded = $this->imageResolver->resolveImageOnMainSite(
                $imagePath,
                intval($article->external_id)
            );

            $image = $this->images->createModel();
            $image->article()->associate($article);
            $image->path = $block->getAttributes()['src'];
            $image->hash = $this->imageHasher->getHash($block);
            $image->external_id = $uploaded->externalId;
            $image->external_url = $uploaded->externalUrl;
            $image->thumbnail_url = $uploaded->thumbnailUrl;
            $image->save();
        } elseif ($this->config->shouldUpdateImages()) {
            $uploaded = $this->imageResolver->resolveImageOnMainSite(
                $imagePath,
                intval($article->external_id)
            );

            $image->path = $block->getAttributes()['src'];
            $image->hash = $this->imageHasher->getHash($block);
            $image->external_id = $uploaded->externalId;
            $image->external_url = $uploaded->externalUrl;
            $image->thumbnail_url = $uploaded->thumbnailUrl;
            $image->save();
        } elseif ($this->config->shouldReplaceImages()) {
            $updated = $this->imageUpdater->uploadImage($imagePath, intval($image->external_id));
            $image->external_url = $updated->externalUrl;
            $image->thumbnail_url = $updated->thumbnailUrl;
            $image->save();
        }

        $image->is_stale = false;
        $image->save();

        $article->image_ids->add($image->getKey());

        $this->imageDiscovery->discoverImageLocalizations($image, $article);

        return $image;
    }
}
