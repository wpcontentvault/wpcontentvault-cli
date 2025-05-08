<?php

declare(strict_types=1);

namespace App\Console\Commands\Checking;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;

class FindArticlesWithoutPreviewCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-articles-without-preview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all discovered articles have images';

    /**
     * Execute the console command.
     */
    public function handle(ArticleRepository $articles): int
    {
        $list = $articles->getAllArticles();

        foreach ($list as $article) {
            $previewPath = $article->path . 'cover/original.png';
            if (false === file_exists($previewPath)) {
                $this->info("Article $article->title ($article->external_id) does not have preview");

                continue;
            }

            $size = getimagesize($previewPath);

            if($size[0] < 500){
                $this->info("Article $article->title ($article->external_id) is to small");
            }
        }

        return self::SUCCESS;
    }
}
