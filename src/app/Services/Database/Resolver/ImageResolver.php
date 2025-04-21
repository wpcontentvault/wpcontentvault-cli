<?php

declare(strict_types=1);

namespace App\Services\Database\Resolver;

use App\Configuration\GlobalConfiguration;
use App\Context\Wordpress\ImageMeta;
use App\Services\Console\ApplicationOutput;
use App\Services\Exporting\AttachedImageFinder;
use App\Services\Exporting\RegexImageFinder;
use App\Services\Wordpress\ImageUploader;
use RuntimeException;

class ImageResolver
{
    public function __construct(
        private GlobalConfiguration $configuration,
        private ImageUploader $uploader,
        private AttachedImageFinder $attachedFinder,
        private RegexImageFinder $regexFinder,
        private ApplicationOutput $output,
    ) {}

    public function resolveImage(string $path, int $postId): ImageMeta
    {
        $this->attachedFinder->replace($postId);

        $this->regexFinder
            ->exclude($this->attachedFinder->getNames())
            ->replace($postId);

        $name = basename($path);

        if ($this->regexFinder->hasImage($name)) {
            return $this->regexFinder->findImageByFileName($name);
        }

        if ($this->attachedFinder->hasImage($name)) {
            return $this->attachedFinder->findImageByFileName($name);
        }

        $this->output->info("Image with $name not found, uploading.");

        if ($this->configuration->shouldThrowOnImageUpload()) {
            throw new RuntimeException('Image uploading is disabled!');
        }

        return $this->uploader->uploadImage($path, $postId);
    }
}
