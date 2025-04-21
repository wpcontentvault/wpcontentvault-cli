<?php

declare(strict_types=1);

namespace App\Providers;

use App\Configuration\AI\Provider\OpenRouterProviderConfiguration;
use App\Configuration\GlobalConfiguration;
use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Factories\WPConnectorFactory;
use App\Registry\ObjectBlockRegistry;
use App\Registry\SitesRegistry;
use App\Services\Console\ApplicationOutput;
use App\Services\Exporting\AttachedImageFinder;
use App\Services\Exporting\RegexImageFinder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(ObjectBlockRegistry::class, function () {
            return new ObjectBlockRegistry;
        });

        $this->app->singleton(SitesRegistry::class, function () {
            return new SitesRegistry($this->app->get(WPConnectorFactory::class));
        });

        $this->app->singleton(ApplicationOutput::class, function () {
            return new ApplicationOutput;
        });

        $this->app->singleton(RegexImageFinder::class, function () {
            /** @var SitesRegistry $config */
            $config = $this->app[SitesRegistry::class];
            $main = $config->getMainSiteConnector();

            return new RegexImageFinder($main);
        });

        $this->app->singleton(AttachedImageFinder::class, function () {
            /** @var SitesRegistry $config */
            $config = $this->app[SitesRegistry::class];

            return new AttachedImageFinder($config);
        });

        $this->app->singleton(GlobalConfiguration::class, function () {
            return new GlobalConfiguration;
        });

        $this->app->bind(AiProviderConfigurationInterface::class, OpenRouterProviderConfiguration::class);
    }
}
