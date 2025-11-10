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
            AiModelEnum::CLAUSE_SONNET_3_5 => 'anthropic/claude-3.5-sonnet',
            AiModelEnum::DEEPSEEK_V3 => 'deepseek/deepseek-chat',
            AiModelEnum::GPT_OSS => 'openai/gpt-oss-120b',
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
}
