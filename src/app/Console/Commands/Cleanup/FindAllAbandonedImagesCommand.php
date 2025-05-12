<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Cleanup\AbandonedImageCleaner;

class FindAllAbandonedImagesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-all-abandoned-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all not used images for article';

    public function handle(ArticleRepository $articles, AbandonedImageCleaner $cleaner): int
    {
        $list = $articles->getAllArticles();

        foreach ($list as $article) {
            $images = $cleaner->findAbandonedImagesForArticle($article);

            foreach ($images as $image) {
                $this->info($image->getBasename());
            }
        }

        return self::SUCCESS;
    }
}
