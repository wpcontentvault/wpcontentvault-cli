<?php

declare(strict_types=1);

namespace App\Console\Commands\Sitemap;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Sitemap\SitemapService;

class GenerateSitemapCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates sitemap for the website';

    /**
     * Execute the console command.
     */
    public function handle(
        SitemapService $sitemapService,
    ): int
    {
        $this->info('Generating sitemap...');

        $sitemapService->updateArticlesSitemap();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
