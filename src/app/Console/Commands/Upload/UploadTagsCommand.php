<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Exporting\TagExporter;
use App\Services\Vault\Iterator\TagDirectoryIterator;

class UploadTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-tags';

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
        TagDirectoryIterator $tagIterator,
        TagExporter          $exporter,
        GlobalConfiguration  $configuration,
    ): int
    {
        $updateTagIds = $this->option('update') ?? false;
        if ($updateTagIds) {
            $configuration->updateTagIds($updateTagIds);
        }

        foreach ($tagIterator->getTagDirectories() as $dir) {
            /** @var \SplFileInfo $dir */
            $exporter->exportTag($dir->getBasename());
        }

        return self::SUCCESS;
    }
}
