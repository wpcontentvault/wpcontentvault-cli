<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Translation\TranslationService;

class LoadArticleTranslationCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load-article-translation {id} {--locale=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse article translations from file to database';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository $locales,
        TranslationService $service,
    ): int {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        $localeCode = $this->option('locale');

        $locale = $locales->findLocaleByCode($localeCode);

        if ($locale === null) {
            $this->error("Locale $localeCode not found!");

            return self::FAILURE;
        }

        $service->loadTranslationsFromStorage($article, $locale);

        return self::SUCCESS;
    }
}
