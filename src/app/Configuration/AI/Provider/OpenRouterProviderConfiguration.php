<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Configuration\AI\AiRequestConfiguration;
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
            AiModelEnum::CLAUDE_SONNET_3_5 => 'anthropic/claude-3.5-sonnet',
            AiModelEnum::CLAUDE_SONNET_4_5 => 'anthropic/claude-4.5-sonnet',
            AiModelEnum::DEEPSEEK_V3 => 'deepseek/deepseek-chat',
            AiModelEnum::GPT_OSS => 'openai/gpt-oss-120b',
            AiModelEnum::MISTRAL_EMBED => 'mistralai/mistral-embed-2312',
        };
    }

    public function getBaseUrl(): string
    {
        return 'https://openrouter.ai/api/';
    }

    public function getEmbeddingsUrl(): string
    {
        return 'v1/embeddings';
    }

    public function getAuthToken(): string
    {
        return $this->accessToken;
    }

    public function buildRequestParams(AiRequestConfiguration $aiConfig): array
    {
        $params = [
            'model' => $this->getModelName($aiConfig->getModel()),
            'temperature' => $aiConfig->getModelConfiguration()->getTemperature(),
            'top_p' => $aiConfig->getModelConfiguration()->getTopP(),
            'top_k' => $aiConfig->getModelConfiguration()->getTopK(),
        ];

        if (null !== $aiConfig->getMOdelConfiguration()->getReasoningEffort()) {
            $params['reasoning'] = [
                'effort' => $aiConfig->getMOdelConfiguration()->getReasoningEffort()
            ];
        }

        return $params;
    }

    public function buildEmbeddingParams(AiRequestConfiguration $aiConfig, string $text): array
    {
        return [
            'model' => $this->getModelName($aiConfig->getModel()),
            'input' => $text,
        ];
    }
}
