<?php

declare(strict_types=1);

namespace App\Console\Commands\Lint;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Console\ApplicationOutput;
use App\Services\Linting\LintService;
use App\Services\Vault\VaultPathResolver;
use Symfony\Component\Finder\Finder;

class LintAllArticlesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lint-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all article files in vault for errors';

    /**
     * Execute the console command.
     */
    public function handle(
        LintService $checkingService,
        VaultPathResolver $pathResolver,
        ApplicationOutput $output
    ): int {
        $finder = new Finder;
        $finder->name('*.*');

        $articlesWithErrors = 0;

        foreach ($finder->directories()->in($pathResolver->getArticlesRoot().'/Imported/') as $dir) {
            $this->info("Parsing $dir");

            $path = 'Imported/'.$dir->getFilename().'/';

            $failed = $checkingService->lintArticleFromPath($path, 'original');

            if ($failed) {
                $articlesWithErrors++;
            }
        }

        $output->info("Articles with errors: $articlesWithErrors");

        return self::SUCCESS;
    }
}
