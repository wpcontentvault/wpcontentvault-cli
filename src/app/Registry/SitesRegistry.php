<?php

declare(strict_types=1);

namespace App\Registry;

use App\Factories\WPConnectorFactory;
use App\Models\Locale;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class SitesRegistry
{
    private ?WPConnectorInterface $mainSiteConnector = null;

    private string $mainSiteLocaleCode;

    private array $connectors;

    public function __construct(WPConnectorFactory $factory)
    {
        $pathResolver = new VaultPathResolver;
        $loader = new VaultConfigLoader;

        $sitesConfig = $loader->loadFromPath($pathResolver->getRoot(), 'sites.json');

        if (($sitesConfig['main'] ?? null) !== null) {
            $this->mainSiteConnector = $factory->make(
                $sitesConfig['main']['domain'],
                $sitesConfig['main']['access_key']
            );

            $this->connectors[$sitesConfig['main']['locale']] = $this->mainSiteConnector;
            $this->mainSiteLocaleCode = $sitesConfig['main']['locale'];
        }

        foreach ($sitesConfig['locales'] as $config) {
            if (isset($config['enabled']) && $config['enabled'] === false) {
                continue;
            }

            $this->connectors[$config['locale']] = $factory->make(
                $config['domain'],
                $config['access_key'],
            );
        }
    }

    public function hasMainSiteConnector(): bool
    {
        return $this->mainSiteConnector !== null;
    }

    public function getMainSiteConnector(): WPConnectorInterface
    {
        if (null === $this->mainSiteConnector) {
            throw new RuntimeException("Main site in not configured!");
        }

        return $this->mainSiteConnector;
    }

    public function hasSiteConnectorForLocale(Locale $locale): bool
    {
        if (isset($this->connectors[$locale->code])) {
            return true;
        }

        return false;
    }

    public function getSiteConnectorByLocale(Locale $locale): WPConnectorInterface
    {
        if (isset($this->connectors[$locale->code])) {
            return $this->connectors[$locale->code];
        }

        throw new RuntimeException("No connectors found for locale {$locale->code}");
    }

    public function isMainSiteConnector(WPConnectorInterface $connector): bool
    {
        if ($this->mainSiteConnector === $connector) {
            return true;
        }

        return false;

    }

    public function getMainSiteLocaleCode(): string
    {
        return $this->mainSiteLocaleCode;
    }
}
