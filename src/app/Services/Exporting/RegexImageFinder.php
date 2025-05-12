<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Context\Wordpress\ImageMeta;
use App\Services\Utils\StringUtils;
use Closure;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Regex\Regex;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorException;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class RegexImageFinder
{
    private WPConnectorInterface $mainConnector;

    private array $images = [];

    private ?int $postId = null;

    private array $excludedImages = [];

    public function __construct(WPConnectorInterface $connector)
    {
        $this->mainConnector = $connector;
    }

    public function exclude(array $excludedImages): self
    {
        $this->excludedImages = $excludedImages;

        return $this;
    }

    public function replace(int $postId): void
    {
        if ($postId === $this->postId) {
            return;
        }

        $this->postId = $postId;

        $postData = $this->mainConnector->getPost($postId);

        $this->images = [];

        $this->extractImageUrls($postData->content, function (string $src, string $alt, int $externalId): void {
            $imageData = $this->getAttachmentOrNull(function () use ($externalId) {
                return $this->mainConnector->getAttachment($externalId);
            });

            if ($imageData === null) {
                dump('Failed to fetch attachment by id: ' . " external_id: $externalId, src: $src, alt: $alt");

                $imageData = $this->getAttachmentOrNull(function () use ($src) {
                    return $this->mainConnector->getAttachmentByUrl(StringUtils::removeImageSize($src));
                });
            }

            if ($imageData === null) {
                dump("Failed to fetch attachment by url $src");

                return;
            }

            if ($imageData->largeUrl == null) {
                throw new RuntimeException("Large thumbnail is empty for image $externalId");
            }

            $srcFileName = StringUtils::removeImageSize(basename($src));
            $obtainedFileName = StringUtils::removeImageSize(basename($imageData->attachmentUrl));

            if ($srcFileName !== $obtainedFileName) {
                dump("Image file name mismatch, src: $srcFileName, remote: $obtainedFileName");
            }

            if ($externalId === 0) {
                dump("External id for $src is zero");

                return;
            }

            $image = new ImageMeta(
                externalId: $externalId,
                externalUrl: $imageData->attachmentUrl,
                thumbnailUrl: $imageData->largeUrl,
            );

            $basename = StringUtils::removeImageSize(basename($src));

            $this->images[$basename] = $image;
        });
    }

    public function hasImage(string $name): bool
    {
        // Some names may be url encoded if they contain special chars like "γ"
        $name = urldecode($name);

        $exists = isset($this->images[$name]);

        if ($exists === false) {
            return false;
        }

        // On multisite for copied articles images may have wrong IDs (from another site media library)
        // Or old articles may contain wrong ids
        $fileName = basename($this->images[$name]->externalUrl);
        $fileNameWithoutSize = StringUtils::removeImageSize($fileName);

        if ($name !== $fileNameWithoutSize) {
            return false;
        }

        return true;
    }

    public function findImageByFileName(string $name): ImageMeta
    {
        $name = urldecode($name);

        if (isset($this->images[$name]) === false) {
            throw new RuntimeException("Image $name not found!");
        }

        return $this->images[$name];
    }

    private function extractImageUrls(string $content, Closure $callable): void
    {
        $dom = new DOMDocument;

        // Suppress conversion errors (from http://bit.ly/pCCRSX)
        \libxml_use_internal_errors(true);
        // Hack to load utf-8 HTML (from http://bit.ly/pVDyCt)
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content);
        $dom->encoding = 'UTF-8';
        \libxml_clear_errors();

        foreach ($dom->getElementsByTagName('img') as $item) {
            /** @var DOMElement $item */
            $class = $item->getAttribute('class');
            $regex = Regex::match('/wp-image-(?P<id>[\d]+)/m', $class);
            $attachmentId = $regex->groupOr('id', '');
            $src = $item->getAttribute('src');

            if (Str::startsWith($src, '//')) {
                $src = 'https' . ':' . $src;
            }

            // Skip images that already resolved using attached image finder
            $imgName = StringUtils::removeImageSize(basename($src));
            if (in_array($imgName, $this->excludedImages)) {
                continue;
            }

            $callable(
                $src,
                $item->getAttribute('alt'),
                intval($attachmentId),
            );
        }
    }

    private function getAttachmentOrNull(Closure $callable): ?AttachmentData
    {
        try {
            return $callable();
        } catch (WPConnectorException $e) {
            dump($e->getMessage());

            return null;
        }
    }
}
