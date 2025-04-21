<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Wordpress\ImageMeta;
use App\Registry\SitesRegistry;
use Illuminate\Support\Str;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ImageUploader
{
    private WPConnectorInterface $mainSiteConnector;

    public function __construct(SitesRegistry $configuration)
    {
        $this->mainSiteConnector = $configuration->getMainSiteConnector();
    }

    public function uploadImage(string $path, int $postId): ImageMeta
    {
        $imageName = basename($path);
        $imageData = file_get_contents($path);

        $response = $this->mainSiteConnector->addAttachment($imageName, $imageData, $postId);

        // videos do not have thumbnails. Other images must have!
        if (Str::endsWith($imageName, 'webm') === false) {
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
