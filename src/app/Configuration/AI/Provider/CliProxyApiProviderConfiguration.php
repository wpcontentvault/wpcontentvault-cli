<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Configuration\AI\AiRequestConfiguration;
use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Enum\AI\AiModelEnum;

class CliProxyApiProviderConfiguration implements AiProviderConfigurationInterface
{
    private ?string $accessToken;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->accessToken = $config['access_token'] ?? null;
        $this->baseUrl = $config['base_url'] ?? 'http://localhost:8317/';
    }

    public function getModelName(AiModelEnum $model): string
    {
        return match ($model) {
            AiModelEnum::GPT_5_5 => 'gpt-5.5',
            default => throw new \RuntimeException("Model {$model->value} is not supported by this provider"),
        };
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
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
        $modelName = $this->getModelName($aiConfig->getModel());

        # Info https://help.router-for.me/configuration/thinking.html
        if (null !== $aiConfig->getMOdelConfiguration()->getReasoningEffort()) {
            if ($aiConfig->getMOdelConfiguration()->getReasoningEffort() == 'high') {
                $modelName .= "(high)";
            } elseif ($aiConfig->getMOdelConfiguration()->getReasoningEffort() == 'medium') {
                $modelName .= "(medium)";
            }
        }

        $params = [
            'model' => $modelName,
            'temperature' => $aiConfig->getModelConfiguration()->getTemperature(),
            'top_p' => $aiConfig->getModelConfiguration()->getTopP(),
            'top_k' => $aiConfig->getModelConfiguration()->getTopK(),
        ];

        return $params;
    }

    public function buildEmbeddingParams(AiRequestConfiguration $aiConfig, string $text): array
    {
        throw new \RuntimeException('Not supported by this provider!');
    }
}
