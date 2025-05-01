<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Finder\Finder;

class FindAbandonedImagesForArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-not-used-images-for-article {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all not used images for article';

    /**
     * Execute the console command.
     */
    public function handle(ArticleRepository $articles): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new \RuntimeException('Article not found!');
        }

        $images = $article->images->keyBy('path');
        $notUsedImages = [];

        $finder = new Finder;
        $finder->name(['*.jpeg', '*.jpg', '*.png', '*.gif', '*.mp4', '*.webm']);
        $finder->exclude(['cover']);
        $finder->sortByName();

        foreach ($finder->files()->in($article->path) as $imageFile) {
            if (false === $images->has($imageFile->getBasename())) {
                $notUsedImages[] = $imageFile;

                $this->info($imageFile->getBasename());
            }
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
