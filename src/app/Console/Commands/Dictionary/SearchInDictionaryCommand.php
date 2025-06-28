<?php

declare(strict_types=1);

namespace App\Console\Commands\Dictionary;

use App\Console\Commands\AbstractApplicationCommand;
use App\Repositories\LocaleRepository;
use App\Services\Database\Dictionary\DictionarySearcher;

class SearchInDictionaryCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-in-dictionary {--source=} {--target=} {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search translations in dictionaries';

    /**
     * Execute the console command.
     */
    public function handle(
        DictionarySearcher $searcher,
        LocaleRepository   $locales,
    ): int
    {
        $sourceCode = $this->option('source');
        $targetCode = $this->option('target');

        $query = $this->argument('query');

        $source = $locales->findLocaleByCode($sourceCode);
        $target = $locales->findLocaleByCode($targetCode);

        $found = $searcher->getTranslationRecommendations($source, $target, $query);

        foreach ($found as $item) {
            $this->info($item);
        }

        return self::SUCCESS;
    }
}
