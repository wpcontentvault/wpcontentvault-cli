<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\ArticleRepository;
use App\Services\Wordpress\LocalizationBindingUpdater;
use RuntimeException;

class UpdateTranslationsBindingsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-translations-bindings {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update translation binding for article on remote website';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        LocalizationBindingUpdater $localizationBindingUpdater
    ): int {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            throw new RuntimeException("Article $id not found");
        }

        $localizationBindingUpdater->updateLocalizationBindingsForArticle($article);

        return self::SUCCESS;
    }
}
