<?php

declare(strict_types=1);

namespace App\Services\Exporting\Factory;

use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Services\Exporting\AttachedImageFinder;

class AttachedImageFinderFactory
{
    private ?AttachedImageFinder $mainFinder = null;
    private array $finders = [];

    public function __construct(
        private SitesRegistry $sitesConfig,
    )
    {
        foreach ($this->sitesConfig->getLocaleCodes() as $code) {
            $this->finders[$code] = null;
        }
    }

    public function getMainFinder(): AttachedImageFinder
    {
        if (null === $this->mainFinder) {
            $this->mainFinder = new AttachedImageFinder($this->sitesConfig->getMainSiteConnector());
        }

        return $this->mainFinder;
    }

    public function getFinderByLocale(Locale $locale): AttachedImageFinder
    {
        if (null === $this->finders[$locale->code]) {
            $this->finders[$locale->code] = new AttachedImageFinder($this->sitesConfig->getSiteConnectorByLocale($locale));
        }

        return $this->finders[$locale->code];
    }
}
