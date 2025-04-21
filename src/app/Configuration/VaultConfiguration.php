<?php

declare(strict_types=1);

namespace App\Configuration;

class VaultConfiguration
{
    public static function getVaultPath(): string
    {
        if (env('WPCONTENTVAULT_APP_ENV') === 'testing') {
            return '/tmp/vault';
        }

        $vaultRoot = env('WPCONTENTVAULT_VAULT_PATH', getcwd().'/vault/');

        if (file_exists($vaultRoot) === false) {
            mkdir($vaultRoot, 0775, true);
        }

        return $vaultRoot;
    }
}
