<?php

declare(strict_types=1);

namespace App\Factories\Ai;

use App\Configuration\AI\Models\Classification\ClassificationGemma3Configuration;
use App\Configuration\AI\Models\EmptyConfiguration;
use App\Configuration\AI\Models\Translation\TranslationClaude35SonnetConfiguration;
use App\Contracts\AI\AiModelConfigurationInterface;
use App\Enum\AI\AiModelEnum;
use RuntimeException;

class AiModelConfigurationFactory
{
    public function makeTranslationConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::CLAUSE_SONNET_3_5 => new TranslationClaude35SonnetConfiguration,
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for translation!"),
        };
    }

    public function makeEmbeddingConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::MXBAI_EMBED_LARGE => new EmptyConfiguration,
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for embedding!"),
        };
    }

    public function makeClassificationConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::GEMMA_3 => new ClassificationGemma3Configuration,
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for embedding!"),
        };
    }
}
