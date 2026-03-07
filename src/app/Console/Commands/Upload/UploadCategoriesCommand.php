<?php

declare(strict_types=1);

namespace App\Console\Commands\Upload;

use App\Configuration\GlobalConfiguration;
use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Exporting\CategoryExporter;
use App\Services\Exporting\TagExporter;
use App\Services\Vault\Iterator\TagDirectoryIterator;

class UploadCategoriesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-categories {--update}';

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
        CategoryRepository       $categoryRepository,
        CategoryExporter         $exporter,
        GlobalConfiguration $configuration,
    ): int
    {
        $updateTagIds = $this->option('update') ?? false;
        if ($updateTagIds) {
            $configuration->updateTermIds($updateTagIds);
        }

        $categoriesList = $categoryRepository->getAllCategories();

        foreach ($categoriesList as $category) {
            $exporter->exportCategory($category->slug);
        }

        return self::SUCCESS;
    }
}
