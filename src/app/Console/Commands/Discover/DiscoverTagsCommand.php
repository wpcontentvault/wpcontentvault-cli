<?php

declare(strict_types=1);

namespace App\Console\Commands\Discover;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Tag;
use App\Models\TagLocalization;
use App\Repositories\LocaleRepository;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Facades\DB;

class DiscoverTagsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discover-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh tags from tags.json';

    /**
     * Execute the console command.
     */
    public function handle(
        LocaleRepository  $locales,
        VaultPathResolver $pathResolver,
        VaultConfigLoader $loader,
    ): int
    {
        $localesList = $locales->getAllLocales()->keyBy('code');

        $tags = $loader->loadFromPath($pathResolver->getRoot(), 'tags.json');

        $localizationsTable = (new TagLocalization())->getTable();

        foreach ($tags as $tagData) {
            $tag = Tag::query()
                ->where('slug', $tagData['slug'])
                ->where('category', $tagData['category'])
                ->first();

            if (null === $tag) {
                $tag = new Tag();
                $tag->slug = $tagData['slug'];
                $tag->category = $tagData['category'];
            }
            $tag->save();

            foreach ($tagData['localizations'] as $code => $localizationData) {
                $locale = $localesList->get($code);

                DB::table($localizationsTable)->updateOrInsert([
                    'tag_id' => $tag->getKey(),
                    'locale_id' => $locale->getKey(),
                ], [
                    'external_id' => $localizationData['external_id'],
                    'name' => $localizationData['name'],
                ]);
            }
        }

        return self::SUCCESS;
    }
}
