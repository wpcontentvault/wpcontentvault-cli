<?php

declare(strict_types = 1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Cleanup\AbandonedImageCleaner;
use Illuminate\Console\Scheduling\Schedule;

class PurgeAbandonedImagesForArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge-abandoned-images-for-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges all not used images for article in vault';

    public function handle(ArticleRepository $articles, AbandonedImageCleaner $cleaner): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new \RuntimeException('Article not found!');
        }

        $cleaner->cleanAbandonedImages($article);

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
