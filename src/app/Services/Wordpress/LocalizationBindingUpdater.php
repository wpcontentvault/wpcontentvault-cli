<?php

declare(strict_types=1);

namespace App\Services\Wordpress;

use App\Models\Article;
use App\Registry\SitesRegistry;

class LocalizationBindingUpdater
{
    public function __construct(
        private SitesRegistry $sitesConfig
    ) {}

    public function updateLocalizationBindingsForArticle(Article $article): void
    {
        $mainSite = $this->sitesConfig->getMainSiteConnector();
        $mainLocaleCode = $this->sitesConfig->getMainSiteLocaleCode();

        foreach ($article->localizations as $localization) {
            if ($localization->locale->code === $mainLocaleCode) {
                continue;
            }

            $mainSite->setTranslation(
                intval($article->external_id),
                $localization->locale->code,
                $localization->external_id
            );
        }

    }
}
