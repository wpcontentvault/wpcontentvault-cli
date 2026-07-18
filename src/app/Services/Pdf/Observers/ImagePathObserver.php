<?php

declare(strict_types=1);

namespace App\Services\Pdf\Observers;

use Typesetterio\Typesetter\Contracts\Chapter;
use Typesetterio\Typesetter\Observers\Observer;

class ImagePathObserver extends Observer
{
    public function __construct(
        private string $rootPath
    ) {}

    #[\Override]
    public function parsed(Chapter $chapter): void
    {
        $html = $chapter->getHtml();

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML('<?xml version="1.0" encoding="utf-8">' . $html);

        foreach ($document->getElementsByTagName("img") as $image) {
            /** @var \DOMElement $image */
            $absolutePath = 'file://' . $this->rootPath . '/' . $image->getAttribute('src');
            $image->setAttribute("src", $absolutePath);
        }

        $newHtml = $document->saveHTML();

        $chapter->setHtml($newHtml);
    }
}
