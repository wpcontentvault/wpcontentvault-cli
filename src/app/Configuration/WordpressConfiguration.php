<?php

declare(strict_types=1);

namespace App\Configuration;

use App\Enum\Wordpress\ImageAlignEnum;
use App\Enum\Wordpress\ImageLinkDestinationEnum;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;

class WordpressConfiguration
{
    private bool $imageLightboxEnabled;

    private ImageLinkDestinationEnum $imageLinkDestination;

    private ImageAlignEnum $imageAlign;

    public function __construct()
    {
        $pathResolver = new VaultPathResolver;
        $loader = new VaultConfigLoader;

        $wordpressConfig = $loader->loadFromPath($pathResolver->getRoot(), 'wordpress.json');

        $blocksConfig = $wordpressConfig['blocks'] ?? [];
        $imageConfig = $blocksConfig['image'] ?? [];

        $this->imageLightboxEnabled = $imageConfig['enable_lightbox'] ?? false;

        if (isset($imageConfig['link_destination'])) {
            $this->imageLinkDestination = ImageLinkDestinationEnum::from($imageConfig['link_destination']);
        } else {
            $this->imageLinkDestination = ImageLinkDestinationEnum::NONE;
        }

        if (isset($imageConfig['align'])) {
            $this->imageAlign = ImageAlignEnum::from($imageConfig['align']);
        } else {
            $this->imageAlign = ImageAlignEnum::CENTER;
        }
    }

    public function isImageLightboxEnabled(): bool
    {
        return $this->imageLightboxEnabled;
    }

    public function getImageLinkDestination(): string
    {
        return $this->imageLinkDestination->value;
    }

    public function getImageAlign(): string
    {
        return $this->imageAlign->value;
    }
}
