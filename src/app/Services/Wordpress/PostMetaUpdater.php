<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use App\Services\Vault\Manifest\V2\ManifestReader;

class PostMetaUpdater
{
    public function __construct(
        private ManifestReader $manifestReader,
        private SitesRegistry $sites,
        private ApplicationOutput $output,
    ) {}

    public function updateTitleAndCategory(string $path, string $name): void
    {
        $meta = $this->manifestReader->loadManifestFromPath($path, $name);
        $connector = $this->sites->getSiteConnectorByLocale($meta->locale);

        $connector->setPostTitle($meta->externalId, $meta->title);

        if ($meta->category !== null) {
            $categoryLocalization = $meta->category->findLocalizationByLocale($meta->locale);
            $connector->setPostCategory($meta->externalId, $categoryLocalization->external_id);
        } else {
            $this->output->info("Category not set for article {$meta->externalId}");
        }
    }

    public function updateLicense(int $postId, string $path, string $name): void
    {
        $meta = $this->manifestReader->loadManifestFromPath($path, $name);
        $connector = $this->sites->getSiteConnectorByLocale($meta->locale);

        $connector->setPostMeta($postId, [
            'license' => $meta->license,
        ]);
    }
}
