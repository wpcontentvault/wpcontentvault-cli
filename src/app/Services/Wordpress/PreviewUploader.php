<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Context\Wordpress\ImageMeta;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class PreviewUploader
{
    public function __construct() {}

    public function uploadPreview(
        string $path,
        int $postId,
        string $imageName,
        WPConnectorInterface $connector
    ): ImageMeta {
        $imageData = file_get_contents($path);

        $response = $connector->addAttachment($imageName, $imageData, $postId);

        return new ImageMeta(
            $response->attachmentId,
            $response->attachmentUrl,
            $response->largeUrl
        );
    }

    public function updatePreview(string $path, int $attachmentId, string $imageName, WPConnectorInterface $connector): ImageMeta
    {
        $imageData = file_get_contents($path);

        $response = $connector->updateAttachment($imageName, $imageData, $attachmentId);

        return new ImageMeta(
            $response->attachmentId,
            $response->attachmentUrl,
            $response->largeUrl
        );
    }
}
