<?php

declare(strict_types=1);

namespace App\Configuration;

use App\Contracts\Extension\ExtensionProviderInterface;
use App\Providers\AppServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Phar;

class ProviderConfiguration
{
    private static array $providers = [
        AppServiceProvider::class,
    ];

    private static array $developmentProviders = [
        IdeHelperServiceProvider::class,
    ];

    public static function getProviders(): array
    {
        $providers = self::$providers;

        if (Phar::running() === '') {
            $providers = array_merge($providers, self::$developmentProviders);
        }

        $providers = array_merge($providers, self::resolveExtensionsProviders());

        return $providers;
    }

    private static function resolveExtensionsProviders(): array
    {
        $vendorPath = Env::get('COMPOSER_VENDOR_DIR') ?: base_path('/vendor');
        $files = new Filesystem;

        if ($files->exists($path = $vendorPath.'/composer/installed.json')) {
            $installed = json_decode($files->get($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        $providers = [];

        foreach ($packages as $package) {
            if (isset($package['extra']['laravel']) === false) {
                continue;
            }

            $isExtension = false;

            foreach ($package['extra']['laravel']['providers'] ?? [] as $provider) {
                if (is_subclass_of($provider, ExtensionProviderInterface::class)) {
                    $isExtension = true;
                }
            }

            if ($isExtension) {
                $providers = array_merge($providers, $package['extra']['laravel']['providers'] ?? []);
            }
        }

        return $providers;
    }
}
