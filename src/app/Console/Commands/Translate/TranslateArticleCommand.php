<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Database\ArticleBuilder;
use App\Services\Database\Cleaner\ParagraphCleaner;
use App\Services\Database\ParagraphParser;
use App\Services\Translation\TranslationService;
use App\Services\Vault\Article\ArticleReader;

class TranslateArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate-article {id}';

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
        TranslationService $service,
        ArticleRepository $articles,
        GlobalConfiguration $config,
        ArticleReader $articleReader,
        ParagraphParser $paragraphParser,
        ParagraphCleaner $paragraphCleaner,
        ArticleBuilder $builder,
    ) {
        $id = $this->argument('id');

        $article = $articles->findArticleByExternalId(intval($id));

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $config->updateImages = true;

        // Re-read article from markdown files
        $article = $articleReader->loadArticleFromPath($article->path);

        // Parse paragraphs
        $paragraphParser->parse($article);

        // Clean up
        $paragraphCleaner->removeStaleParagraphs();

        foreach ($article->localizations as $localization) {
            // Skip for original
            if ($article->locale->code === $localization->locale->code) {
                continue;
            }

            $service->generateMissingTranslationsForArticle($article, $localization->locale);

            $builder->buildTranslation($article, $localization->locale);
        }

        return self::SUCCESS;
    }
}
