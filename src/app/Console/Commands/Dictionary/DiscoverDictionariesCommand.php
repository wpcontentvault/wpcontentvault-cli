<?php

declare(strict_types=1);

namespace App\Console\Commands\Dictionary;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Database\Dictionary\DictionaryLoader;

class DiscoverDictionariesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-dictionaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads all dictionaries from vault';

    /**
     * Execute the console command.
     */
    public function handle(
        DictionaryLoader $dictionaryLoader
    ): int {
        $dictionaryLoader->loadDictionariesFromConfig();

        return self::SUCCESS;
    }
}
