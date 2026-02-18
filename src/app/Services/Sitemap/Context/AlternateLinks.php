<?php

declare(strict_types=1);

namespace App\Services\Sitemap\Context;

class AlternateLinks
{
    public array $links = [];

    public function __construct() {}

    public function addLink(string $url, string $localeCode): self
    {
        $this->links[] = new AlternateLink($url, $localeCode);
        return $this;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
}
