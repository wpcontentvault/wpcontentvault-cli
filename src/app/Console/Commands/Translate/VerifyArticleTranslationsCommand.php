<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Database\ArticleTranslationLoader;
use RuntimeException;

class VerifyArticleTranslationsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify-article-translations {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ArticleTranslationLoader $translationLoader
    ) {
        $id = $this->argument('id');

        $article = $articles->findArticleByExternalId(intval($id));

        if ($article === null) {
            throw new RuntimeException("Article $id not found");
        }

        foreach ($article->localizations as $localization) {
            $translationLoader->fetchAllTranslationsFromStorage($article, $localization->locale);
        }

        return self::SUCCESS;
    }
}
