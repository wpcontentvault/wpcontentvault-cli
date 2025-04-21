<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Context\Wordpress\ImageMeta;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;

class AttachedImageFinder
{
    private SitesRegistry $sitesRegistry;

    private array $images = [];

    private ?int $postId = null;

    public function __construct(SitesRegistry $sitesRegistry)
    {
        $this->sitesRegistry = $sitesRegistry;
    }

    public function replace(int $postId, ?Locale $locale = null): void
    {
        if ($postId === $this->postId) {
            return;
        }

        $this->postId = $postId;

        if ($locale !== null) {
            $connector = $this->sitesRegistry->getSiteConnectorByLocale($locale);
        } else {
            $connector = $this->sitesRegistry->getMainSiteConnector();
        }

        $attachments = $connector->query()->parent($postId)->getAttachments();

        foreach ($attachments->posts as $attachment) {
            /** @var AttachmentData $attachment */
            $name = basename($attachment->attachmentUrl);
            $this->images[$name] = new ImageMeta(
                externalId: $attachment->attachmentId,
                externalUrl: $attachment->attachmentUrl,
                thumbnailUrl: $attachment->largeUrl,
            );
        }
    }

    public function hasImage(string $name): bool
    {
        $exists = isset($this->images[$name]);

        if ($exists === false) {
            return false;
        }

        return true;
    }

    public function findImageByFileName(string $name): ImageMeta
    {
        if (isset($this->images[$name]) === false) {
            throw new RuntimeException("Image $name not found!");
        }

        return $this->images[$name];
    }

    public function getNames(): array
    {
        return array_keys($this->images);
    }
}
