<?php

declare(strict_types=1);

namespace App\Services\Database\Cleaner;

use App\Models\Tag;
use App\Repositories\TagRepository;

class TagCleaner
{
    public function __construct(
        private TagRepository $tags
    ) {}

    public function markTagsAsStale(array $ids): void
    {
        foreach ($ids as $id) {
            $text = $this->tags->findTagBySlug($id);
            $text->is_stale = true;
            $text->save();
        }
    }

    public function removeStaleTags(): void
    {
        $this->tags->getStaleQuery()->each(function (Tag $tag): void {
            $tag->delete();
        });
    }
}
