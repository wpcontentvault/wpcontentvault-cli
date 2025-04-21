<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Locale;
use App\Registry\SitesRegistry;
use App\Repositories\LocaleRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Importing\ArticleImporter;
use Throwable;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostData;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ImportArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-articles {--page=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all available articles from WordPress';

    public function __construct(
        private ArticleImporter $importer
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(
        SitesRegistry $sitesConfiguration,
        LocaleRepository $locales,
        ApplicationOutput $output
    ): void {
        $locale = $locales->findLocaleByCode('ru');
        $mainSite = $sitesConfiguration->getMainSiteConnector();

        $perPage = 10;
        $page = intval($this->option('page') ?? 1);
        $hasNext = true;

        while ($hasNext) {
            $output->info('Importing page: '.$page);

            $posts = $mainSite->query()->page($page)->count($perPage)->getPosts();

            $this->processPosts($posts->posts, $mainSite, $locale, $output);

            $hasNext = $posts->hasMore;

            if ($hasNext) {
                $page++;
            }
        }

    }

    public function processPosts(array $posts, WPConnectorInterface $connector, Locale $locale, ApplicationOutput $output): void
    {
        foreach ($posts as $post) {
            /** @var PostData $post */
            try {
                $output->info('Importing article '.$post->id);

                $this->importer->importArticle($post->id, $connector, $locale, 'original');
            } catch (Throwable $e) {
                $output->error($e->getMessage());
                $output->error('Post id: '.$post->id);

                throw $e;
            }
        }
    }
}
