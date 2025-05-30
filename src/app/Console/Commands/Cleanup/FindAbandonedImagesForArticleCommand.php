<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Cleanup\AbandonedImageCleaner;
use Illuminate\Console\Scheduling\Schedule;

class FindAbandonedImagesForArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-abandoned-images {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all not used images for article';

    /**
     * Execute the console command.
     */
    public function handle(ArticleRepository $articles, AbandonedImageCleaner $cleaner): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new \RuntimeException('Article not found!');
        }

        $images = $cleaner->findAbandonedImagesForArticle($article);

        foreach ($images as $image) {
            $this->info($image->getBasename());
        }

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
