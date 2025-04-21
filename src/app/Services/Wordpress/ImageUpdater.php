<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Wordpress\ImageMeta;
use App\Registry\SitesRegistry;
use Illuminate\Support\Str;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ImageUpdater
{
    private WPConnectorInterface $mainSiteConnector;

    public function __construct(SitesRegistry $configuration)
    {
        $this->mainSiteConnector = $configuration->getMainSiteConnector();
    }

    public function uploadImage(string $path, int $attachmentId): ImageMeta
    {
        $imageName = basename($path);
        $imageData = file_get_contents($path);

        $response = $this->mainSiteConnector->updateAttachment($imageName, $imageData, $attachmentId);

        if (Str::endsWith($imageName, 'm4v') === false) {
            if (empty($response->attachmentUrl) || empty($response->largeUrl)) {
                throw new RuntimeException('Attachment returned not complete data!');
            }
        }

        return new ImageMeta(
            $response->attachmentId,
            $response->attachmentUrl,
            $response->largeUrl
        );
    }
}
