<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Registry\SitesRegistry;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Wordpress\PreviewUploader;

class PreviewExporter
{
    public function __construct(
        private SitesRegistry       $sites,
        private ManifestReader      $manifestReader,
        private AttachedImageFinder $imageFinder,
        private PreviewUploader     $uploader,
    ) {}

    public function setCover(string $path, string $name): void
    {
        $meta = $this->manifestReader->loadManifestFromPath($path, $name);
        $connector = $this->sites->getSiteConnectorByLocale($meta->locale);
        $this->imageFinder->replace($meta->externalId, $meta->locale);

        $coverName = 'preview-' . $meta->externalId . '.png';

        $vaultFilePath = $path . 'cover/original.png';
        if (file_exists($path . 'cover/' . $name . '.png')) {
            $vaultFilePath = $path . 'cover/' . $name . '.png';
        }

        if ($this->imageFinder->hasImage($coverName) === false) {
            $attachment = $this->uploader->uploadPreview(
                $vaultFilePath,
                $meta->externalId,
                $coverName,
                $connector
            );
        } else {
            $attachment = $this->imageFinder->findImageByFileName($coverName);

            $this->uploader->updatePreview(
                $vaultFilePath,
                $attachment->externalId,
                $coverName,
                $connector,
            );
        }

        $connector->setPostThumbnail($meta->externalId, $attachment->externalId);
    }
}
