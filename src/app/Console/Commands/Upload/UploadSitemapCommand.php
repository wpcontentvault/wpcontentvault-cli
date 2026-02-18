<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Exporting\TagExporter;
use App\Services\Vault\Iterator\TagDirectoryIterator;
use App\Services\Wordpress\SitemapUpdater;

class UploadSitemapCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads sitemap to wordpress site';

    /**
     * Execute the console command.
     */
    public function handle(
        SitemapUpdater $sitemapUpdater,
    ): int
    {
        $sitemapUpdater->updateSitemap();

        return self::SUCCESS;
    }
}
