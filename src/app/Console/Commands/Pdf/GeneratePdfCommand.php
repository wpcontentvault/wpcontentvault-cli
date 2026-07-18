<?php

declare(strict_types=1);

namespace App\Console\Commands\Pdf;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Pdf\PdfGenerationService;

class GeneratePdfCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-pdf {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates pdf files for article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository   $articles,
        PdfGenerationService $service
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        foreach($article->localizations as $localization) {
            $service->generatePdf($article, $localization->locale);
        }

        return self::SUCCESS;
    }
}
