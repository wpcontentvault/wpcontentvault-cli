<?php

declare(strict_types=1);

namespace App\Console\Commands\Lint;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Linting\LintService;
use Illuminate\Console\Command;

class LintArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lint-article {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check article file for errors';

    /**
     * Execute the console command.
     */
    public function handle(LintService $checkingService): int
    {
        $path = $this->argument('path');

        $checkingService->lintArticleFromPath($path, 'original');

        return self::SUCCESS;
    }
}
