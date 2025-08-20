<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Manifest\V1\ManifestReader;

class SetOriginalLocaleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-article-original-locale {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets article original locale';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocaleRepository  $locales,
        ManifestReader    $manifestLoader,
    ): int
    {
        $id = $this->argument('id');
        $localeCode = $this->argument('locale');

        $article = $articles->findArticleByExternalId(intval($id));
        if ($article === null) {
            throw new \RuntimeException('Article not found!');
        }

        $locale = $locales->findLocaleByCode($localeCode);
        if ($locale === null) {
            throw new \RuntimeException('Locale not found!');
        }

        $originalMeta = $manifestLoader->loadManifestFromPath($article->path, 'original');

        if ($article->locale !== $originalMeta->locale) {
            $this->error("Article original locale form DB and in Manifest mismatch.");

            return self::FAILURE;
        }

        if ($originalMeta->locale->code === $localeCode) {
            $this->error("Locale {$localeCode} is already original!");

            return self::FAILURE;
        }

        $oldOriginalCode = $originalMeta->locale->code;
        $newOriginalCode = $localeCode;

        if(false === file_exists($article->path . $newOriginalCode . '.json')) {
            $this->error("Manifest for locale {$localeCode} does not exist for article!");

            return self::FAILURE;
        }

        rename($article->path . 'original.json', $article->path . 'temp_original.json');
        rename($article->path . 'original.md', $article->path . 'temp_original.md');

        rename($article->path . $newOriginalCode . '.md', $article->path . 'original.md');
        rename($article->path . $newOriginalCode . '.json', $article->path . 'original.json');

        rename($article->path . 'temp_original.json', $article->path . $oldOriginalCode . '.json');
        rename($article->path . 'temp_original.md', $article->path . $oldOriginalCode . '.md');

        return self::SUCCESS;
    }
}
