<?php

declare(strict_types=1);

namespace App\Console\Commands\Translate;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Database\ParagraphParser;

class ParseParagraphsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse-paragraphs {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parae translatable paragraphs from parsed article';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ParagraphParser $paragraphParser,
    ) {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article $id not found!");

            return self::FAILURE;
        }

        $paragraphParser->parse($article);

        return self::SUCCESS;
    }
}
