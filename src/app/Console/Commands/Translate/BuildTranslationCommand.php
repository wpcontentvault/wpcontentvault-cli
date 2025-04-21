<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Database\ArticleBuilder;

class BuildTranslationCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build-article-translation {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds article translation from database to a file';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository $locales,
        ArticleBuilder $builder,
    ) {
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

        $builder->buildTranslation($article, $locale);

        return self::SUCCESS;
    }
}
