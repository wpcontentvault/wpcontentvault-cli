<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Database\Cleaner\ParagraphCleaner;

class ResetTranslationCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-translation {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes translation cache for article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ParagraphCleaner $paragraphCleaner,
    ) {
        $id = $this->argument('id');

        $article = $articles->findArticleByExternalId(intval($id));

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $paragraphCleaner->removeTranslatedParagraphs($article);

        return self::SUCCESS;
    }
}
