<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Registry\SitesRegistry;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Wordpress\PreviewUploader;

class PreviewExporter
{
    public function __construct(
        private SitesRegistry $sites,
        private ManifestReader $manifestReader,
        private AttachedImageFinder $imageFinder,
        private PreviewUploader $uploader,
    ) {}

    public function setCover(string $path, string $name): void
    {
        $meta = $this->manifestReader->loadManifestFromPath($path, $name);
        $connector = $this->sites->getSiteConnectorByLocale($meta->locale);
        $this->imageFinder->replace($meta->externalId, $meta->locale);

        $coverName = 'preview-'.$meta->externalId.'.png';

        if ($this->imageFinder->hasImage($coverName) === false) {
            $attachment = $this->uploader->uploadPreview(
                $path.'cover/original.png',
                $meta->externalId,
                $coverName,
                $connector
            );
        } else {
            $attachment = $this->imageFinder->findImageByFileName($coverName);

            $this->uploader->updatePreview(
                $path.'cover/original.png',
                $attachment->externalId,
                $coverName,
                $connector,
            );
        }

        $connector->setPostThumbnail($meta->externalId, $attachment->externalId);
    }
}
