<?php

declare(strict_types=1);

namespace App\Services\Database\Resolver;

use App\Configuration\GlobalConfiguration;
use App\Context\Wordpress\ImageMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use App\Services\Exporting\Factory\AttachedImageFinderFactory;
use App\Services\Exporting\RegexImageFinder;
use App\Services\Wordpress\ImageUploader;
use RuntimeException;

class ImageDataResolver
{
    public function __construct(
        private SitesRegistry $sitesConfig,
        private GlobalConfiguration $configuration,
        private ImageUploader $uploader,
        private RegexImageFinder $regexFinder,
        private ApplicationOutput $output,
        private AttachedImageFinderFactory $attachedFinderFactory,
    ) {}

    public function resolveImageOnMainSite(string $path, int $postId): ImageMeta
    {
        $attachedFinder = $this->attachedFinderFactory->getMainFinder();
        $attachedFinder->replace($postId);

        $this->regexFinder
            ->exclude($attachedFinder->getNames())
            ->replace($postId);

        $name = basename($path);

        if ($this->regexFinder->hasImage($name)) {
            return $this->regexFinder->findImageByFileName($name);
        }

        if ($attachedFinder->hasImage($name)) {
            return $attachedFinder->findImageByFileName($name);
        }

        $this->output->info("Image with $name not found, uploading.");

        if ($this->configuration->shouldThrowOnImageUpload()) {
            throw new RuntimeException('Image uploading is disabled!');
        }

        return $this->uploader->uploadImage($path, $postId, $this->sitesConfig->getMainSiteConnector());
    }

    public function resolveImageOnSiteByLocale(string $path, int $postId, Locale $locale): ImageMeta
    {
        $attachedFinder = $this->attachedFinderFactory->getFinderByLocale($locale);
        $attachedFinder->replace($postId);

        $name = basename($path);

        if ($attachedFinder->hasImage($name)) {
            return $attachedFinder->findImageByFileName($name);
        }

        $this->output->info("Image with $name not found, uploading.");

        if ($this->configuration->shouldThrowOnImageUpload()) {
            throw new RuntimeException('Image uploading is disabled!');
        }

        return $this->uploader->uploadImage($path, $postId, $this->sitesConfig->getSiteConnectorByLocale($locale));
    }
}
