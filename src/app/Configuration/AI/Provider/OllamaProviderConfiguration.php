<?php

declare(strict_types=1);

namespace App\Configuration\AI\Provider;

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
            AiModelEnum::CLAUSE_SONNET_3_5 => '',
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
}
