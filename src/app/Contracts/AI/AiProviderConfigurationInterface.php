<?php

declare(strict_types=1);

namespace App\Contracts\AI;

use App\Configuration\AI\AiRequestConfiguration;
use App\Enum\AI\AiModelEnum;

interface AiProviderConfigurationInterface
{
    public function getModelName(AiModelEnum $model): string;

    public function getBaseUrl(): string;

    public function getEmbeddingsUrl(): string;

    public function getAuthToken(): string;

    public function buildRequestParams(AiRequestConfiguration $aiConfig): array;
}
