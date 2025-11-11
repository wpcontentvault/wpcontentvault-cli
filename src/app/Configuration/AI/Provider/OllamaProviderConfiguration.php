<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

use App\Configuration\AI\AiRequestConfiguration;
use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Enum\AI\AiModelEnum;
use Illuminate\Support\Str;

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
            AiModelEnum::CLAUDE_SONNET_3_5 => '',
            AiModelEnum::CLAUDE_SONNET_4_5 => '',
            AiModelEnum::DEEPSEEK_V3 => '',
            AiModelEnum::MXBAI_EMBED_LARGE => 'mxbai-embed-large',
            AiModelEnum::GEMMA_3 => 'gemma3:12b',
            AiModelEnum::DEEPSEEK_R1 => 'deepseek-r1:14b',
        };
    }

    public function getBaseUrl(): string
    {
        return Str::finish($this->baseUrl, '/');
    }

    public function getEmbeddingsUrl(): string
    {
        return 'api/embeddings';
    }

    public function getAuthToken(): string
    {
        return '';
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
            $params['think'] = true;
        }

        return $params;
    }

    public function buildEmbeddingParams(AiRequestConfiguration $aiConfig, string $text): array
    {
        return [
            'model' => $this->getModelName($aiConfig->getModel()),
            'prompt' => $text,
        ];
    }
}
