<?php

declare(strict_types=1);

namespace App\Contracts\CommonMark;

use Illuminate\Support\Collection;
use League\CommonMark\Node\Block\Document;

interface DocumentObjectRendererInterface
{
    /**
     * Render the given Document node (and all of its children)
     */
    public function renderDocument(Document $document): Collection;
}
