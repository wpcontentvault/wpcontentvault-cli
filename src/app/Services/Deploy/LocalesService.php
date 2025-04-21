<?php

declare(strict_types=1);

namespace App\Services\Deploy;

use App\Models\Locale;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Facades\DB;

class LocalesService
{
    public function __construct(
        private VaultPathResolver $vaultPathResolver,
        private VaultConfigLoader $vaultConfigLoader
    ) {}

    public function refreshLocalesFromConfig(): void
    {
        $table = (new Locale)->getTable();
        $locales = $this->vaultConfigLoader->loadFromPath(
            $this->vaultPathResolver->getRoot(),
            'locales.json'
        );

        foreach ($locales as $locale) {
            DB::table($table)->updateOrInsert(['code' => $locale['code']], [
                'name' => $locale['name'],
                'options' => json_encode($locale['options'] ?? [], JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
}
