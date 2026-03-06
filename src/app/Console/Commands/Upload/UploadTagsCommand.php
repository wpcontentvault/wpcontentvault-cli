<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\TagRepository;
use App\Services\Exporting\TagExporter;
use App\Services\Vault\Iterator\TagDirectoryIterator;

class UploadTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-tags {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all tags to WordPress';

    /**
     * Execute the console command.
     */
    public function handle(
        TagRepository       $tagRepository,
        TagExporter         $exporter,
        GlobalConfiguration $configuration,
    ): int
    {
        $updateTagIds = $this->option('update') ?? false;
        if ($updateTagIds) {
            $configuration->updateTagIds($updateTagIds);
        }

        $tagsList = $tagRepository->getAllTags();

        foreach ($tagsList as $tag) {
            $exporter->exportTag($tag->path);
        }

        return self::SUCCESS;
    }
}
