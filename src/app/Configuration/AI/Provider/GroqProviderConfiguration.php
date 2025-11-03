<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Enum\AI\AiModelEnum;

class GroqProviderConfiguration implements AiProviderConfigurationInterface
{
    private ?string $accessToken;

    public function __construct(array $config)
    {
        $this->accessToken = $config['access_token'] ?? null;
    }

    public function getModelName(AiModelEnum $model): string
    {
        return match ($model) {
            AiModelEnum::GPT_OSS => 'openai/gpt-oss-120b',
        };
    }

    public function getBaseUrl(): string
    {
        return 'https://api.groq.com/openai/';
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
