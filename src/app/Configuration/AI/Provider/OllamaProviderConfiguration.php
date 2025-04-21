<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Enum\AI\AiModelEnum;

class OllamaProviderConfiguration implements AiProviderConfigurationInterface
{
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'] ?? 'http://localhost';
    }

    public function getModelName(AiModelEnum $model): string
    {
        return match ($model) {
            AiModelEnum::CLAUSE_SONNET_3_5 => '',
            AiModelEnum::DEEPSEEK_V3 => '',
        };
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getAuthToken(): string
    {
        return '';
    }
}
