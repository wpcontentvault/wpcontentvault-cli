<?php

declare(strict_types=1);

namespace App\Contracts\AI;

interface AiRequestConfigurationInterface
{
    public function getModelConfiguration(): AiModelConfigurationInterface;

    public function getProviderConfiguration(): AiProviderConfigurationInterface;
}
