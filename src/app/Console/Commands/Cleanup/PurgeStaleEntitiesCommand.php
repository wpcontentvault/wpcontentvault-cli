<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Models\Image;
use App\Registry\SitesRegistry;
use App\Repositories\ImageRepository;
use App\Services\Database\Cleaner\ParagraphCleaner;
use App\Services\Database\Cleaner\TagCategoryCleaner;
use App\Services\Database\Cleaner\TagCleaner;
use Illuminate\Console\Command;

class PurgeStaleEntitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge-stale-entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all stale entities from database';

    /**
     * Execute the console command.
     */
    public function handle(
        ParagraphCleaner $paragraphCleaner,
        TagCleaner $tagCleaner,
        TagCategoryCleaner $tagCategoryCleaner,
        ImageRepository $images,
        SitesRegistry $configuration
    ): int {
        $mainSite = $configuration->getMainSiteConnector();

        $paragraphCleaner->removeStaleParagraphs();
        $tagCleaner->removeStaleTags();
        $tagCategoryCleaner->removeStaleCategories();

        $images->getStaleQuery()->each(function (Image $img): void {
            // $mainSite->deleteAttachment($img->external_id);

            $img->delete();
        });

        return self::SUCCESS;
    }
}
