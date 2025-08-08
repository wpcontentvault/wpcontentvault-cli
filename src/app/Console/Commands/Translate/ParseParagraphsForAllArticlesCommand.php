<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Database\ParagraphParser;

class ParseParagraphsForAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse-paragraphs-for-articles {--year=} {--continue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parae translatable paragraphs for all articles';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ParagraphParser   $paragraphParser,
    )
    {
        $year = $this->option('year');
        $continue = $this->option('continue');

        if (null === $year) {
            $articlesList = $articles->getAllArticles();
        } else {
            $articlesList = $articles->getArticlesByYear(intval($year));
        }

        foreach ($articlesList as $article) {
            if ($continue !== null && $article->external_id !== intval($continue)) {
                continue;
            } else {
                $continue = null;
            }

            $this->info("Parsing {$article->title} ({$article->external_id})");
            $paragraphParser->parse($article);
            $this->info("Done.");
        }

        return self::SUCCESS;
    }
}
