<?php

declare(strict_types=1);

namespace App\Services\Database\Cleaner;

use App\Repositories\ImageRepository;

class ImageCleaner
{
    public function __construct(
        private ImageRepository $images
    ) {}

    public function markImagesAsStale(array $ids): void
    {
        foreach ($ids as $id) {
            $image = $this->images->findImageByUuid($id);
            $image->is_stale = true;
            $image->save();
        }
    }
}
