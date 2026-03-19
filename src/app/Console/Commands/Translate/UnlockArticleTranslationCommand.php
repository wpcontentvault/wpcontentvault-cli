<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Database\ArticleTranslationLoader;
use RuntimeException;

class UnlockArticleTranslationCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unlock-article-translation {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlocks article translation for locale';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository  $locales,
    )
    {
        $id = $this->argument('id');

        $article = $articles->findArticleByExternalId(intval($id));

        if ($article === null) {
            throw new RuntimeException("Article $id not found");
        }

        $code = $this->argument('locale');
        $locale = $locales->findLocaleByCode($code);
        if (null === $locale) {
            throw new RuntimeException("Locale $code not found");
        }

        $filePath = $article->path . '/' . $locale->code . '.lock';

        if (file_exists($filePath)) {
            unlink($filePath);

            $this->info("Article translation for $id with locale $code was unlocked");
        } else {
            $this->info("Article translation for $id with locale $code wasn't locked");
        }

        return self::SUCCESS;
    }
}
