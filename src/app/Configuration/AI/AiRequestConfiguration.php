<?php

declare(strict_types=1);

namespace App\Configuration\AI;

use App\Contracts\AI\AiModelConfigurationInterface;
use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Contracts\AI\AiRequestConfigurationInterface;
use App\Enum\AI\AiModelEnum;

class AiRequestConfiguration implements AiRequestConfigurationInterface
{
    public function __construct(
        private AiProviderConfigurationInterface $provider,
        private AiModelEnum $model,
        private AiModelConfigurationInterface $configuration,
    ) {}

    public function getModel(): AiModelEnum
    {
        return $this->model;
    }

    public function getModelConfiguration(): AiModelConfigurationInterface
    {
        return $this->configuration;
    }

    public function getProviderConfiguration(): AiProviderConfigurationInterface
    {
        return $this->provider;
    }
}
