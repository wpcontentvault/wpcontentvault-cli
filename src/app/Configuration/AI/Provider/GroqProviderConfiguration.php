<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Configuration\AI\AiRequestConfiguration;
use App\Contracts\AI\AiModelConfigurationInterface;
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

    public function buildRequestParams(AiRequestConfiguration $aiConfig): array
    {
        //top_k not supported by groq
        $params = [
            'model' => $this->getModelName($aiConfig->getModel()),
            'temperature' => $aiConfig->getModelConfiguration()->getTemperature(),
            'top_p' => $aiConfig->getModelConfiguration()->getTopP(),
        ];

        if (null !== $aiConfig->getMOdelConfiguration()->getReasoningEffort()) {
            $params['reasoning_effort'] = $aiConfig->getMOdelConfiguration()->getReasoningEffort();
        }

        return $params;
    }

    public function buildEmbeddingParams(AiRequestConfiguration $aiConfig, string $text): array
    {
        throw new \RuntimeException('Not supported by this provider!');
    }
}
