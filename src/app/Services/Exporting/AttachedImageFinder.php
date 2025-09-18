<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Context\Wordpress\ImageMeta;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class AttachedImageFinder
{
    private WPConnectorInterface $connector;

    private array $images = [];

    private ?int $postId = null;

    public function __construct(WPConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public function replace(int $postId): void
    {
        if ($postId === $this->postId) {
            return;
        }

        $this->postId = $postId;

        $attachments = $this->connector->query()->parent($postId)->getAttachments();

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
