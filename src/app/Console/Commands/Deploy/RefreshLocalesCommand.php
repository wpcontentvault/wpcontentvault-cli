<?php

declare(strict_types=1);

namespace App\Console\Commands\Deploy;

use App\Services\Deploy\LocalesService;
use Illuminate\Console\Command;

class RefreshLocalesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-locales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh locales in the database based on locales.json file';

    /**
     * Execute the console command.
     */
    public function handle(LocalesService $service): int
    {
        $service->refreshLocalesFromConfig();

        return self::SUCCESS;
    }
}
