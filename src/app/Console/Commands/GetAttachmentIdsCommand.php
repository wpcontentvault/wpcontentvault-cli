<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\ArticleRepository;

class GetAttachmentIdsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-attachment-ids {--year=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all attachment ids for year';

    /**
     * Execute the console command.
     */

    public function handle(ArticleRepository $articles): int
    {
        $year = $this->option('year');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        foreach ($articlesList as $article) {
            foreach ($article->images as $image) {
                $this->info($image->external_id);
            }
        }

        return self::SUCCESS;
    }
}
