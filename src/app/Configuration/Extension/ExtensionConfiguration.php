<?php

declare(strict_types=1);

namespace App\Configuration\Extension;

use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;

abstract class ExtensionConfiguration
{
    private bool $enabled;

    private array $settings = [];

    public function __construct(
        VaultPathResolver $pathResolver,
        VaultConfigLoader $configLoader,
    ) {
        $root = $pathResolver->getRoot();
        $name = $this->getExtensionName();

        if ($configLoader->configExists($root.'/extensions/', $name.'.json')) {
            $config = $configLoader->loadFromPath($root.'extensions/', $name.'.json');
        } else {
            $config = [];
        }

        $this->enabled = $config['enabled'] ?? false;
        $this->settings = $config['settings'] ?? [];
    }

    abstract public function getExtensionName(): string;

    public function isExtensionEnabled(): bool
    {
        return $this->enabled;
    }

    public function getExtensionSettings(): array
    {
        return $this->settings;
    }
}
