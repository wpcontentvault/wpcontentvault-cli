<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\ArticleRepository;

class ArticleInfoCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'info {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prints info about articles by ID';

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

        $this->info("Title: " . $article->title);
        $this->info("Path: " . $article->path);
        $this->info('Images count: ' . count($article->images));

        $this->info("Localizations:");
        foreach($article->localizations as $localization) {
            $this->info("");

            $this->info(" - language: " . $localization->locale->name);
            $this->info(' - url: ' . $localization->url);
            $this->info(' - external_id: ' . $localization->external_id);
        }

        $this->info("");

        return self::SUCCESS;
    }

}
