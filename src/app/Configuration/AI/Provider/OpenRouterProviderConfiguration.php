<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Enum\AI\AiModelEnum;

class OpenRouterProviderConfiguration implements AiProviderConfigurationInterface
{
    private ?string $accessToken;

    public function __construct(array $config)
    {
        $this->accessToken = $config['access_token'] ?? null;
    }

    public function getModelName(AiModelEnum $model): string
    {
        return match ($model) {
            AiModelEnum::CLAUSE_SONNET_3_5 => 'anthropic/claude-3.5-sonnet',
            AiModelEnum::DEEPSEEK_V3 => 'deepseek/deepseek-chat',
        };
    }

    public function getBaseUrl(): string
    {
        return 'https://openrouter.ai/api/';
    }

    public function getEmbeddingsUrl(): string
    {
        throw new \RuntimeException('Not supported by this provider!');
    }

    public function getAuthToken(): string
    {
        return $this->accessToken;
    }
}
