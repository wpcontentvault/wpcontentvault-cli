<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use _PHPStan_ea7072c0a\Symfony\Component\String\Exception\RuntimeException;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Wordpress\PostMetaUpdater;

class UpdateArticleTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-article-tags {id} {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates article tags on remote website';

    /**
     * Execute the console command.
     */

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository    $articles,
        LocaleRepository     $locales,
        ManifestNameResolver $manifestNameResolver,
        PostMetaUpdater      $postMetaUpdater,
    ): int
    {
        $id = intval($this->argument('id'));
        $localeCode = $this->argument('locale');

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

        $locale = $locales->findLocaleByCode($localeCode);
        if ($locale === null) {
            throw new RuntimeException('Locale not found!');
        }

        $name = $manifestNameResolver->resolveName($article, $locale);

        $postMetaUpdater->updateTags($article->path, $name);

        return self::SUCCESS;
    }
}
