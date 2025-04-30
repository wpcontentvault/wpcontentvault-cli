<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Translation\TranslationService;

class TranslateArticleToLocaleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate-article-to-locale {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate translations only for a specific locale';

    /**
     * Execute the console command.
     */
    public function handle(TranslationService $service, ArticleRepository $articles, LocaleRepository $locales): int
    {
        $id = intval($this->argument('id'));
        $localeCode = $this->argument('locale');

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $locale = $locales->findLocaleByCode($localeCode);

        if ($locale === null) {
            $this->error("Locale $localeCode not found!");

            return self::FAILURE;
        }

        $service->generateMissingTranslationsForArticle($article, $locale);

        $this->info('Done.');

        return self::SUCCESS;
    }
}
