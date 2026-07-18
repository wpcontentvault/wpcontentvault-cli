<?php

declare(strict_types=1);

namespace App\Factories\Ai;

use App\Configuration\AI\Models\Classification\ClassificationClaude35Configuration;
use App\Configuration\AI\Models\Classification\ClassificationDeepseekR1Configuration;
use App\Configuration\AI\Models\Classification\ClassificationGemma3Configuration;
use App\Configuration\AI\Models\Classification\ClassificationGptOssConfiguration;
use App\Configuration\AI\Models\EmptyConfiguration;
use App\Configuration\AI\Models\Summarize\SummarizeClaude35Configuration;
use App\Configuration\AI\Models\Summarize\SummarizeGemma3Configuration;
use App\Configuration\AI\Models\Summarize\SummarizeGptOssConfiguration;
use App\Configuration\AI\Models\Translation\TranslationClaude35SonnetConfiguration;
use App\Configuration\AI\Models\Translation\TranslationClaude45SonnetConfiguration;
use App\Configuration\AI\Models\Translation\TranslationClaude47OpusConfiguration;
use App\Configuration\AI\Models\Translation\TranslationGpt55Configuration;
use App\Configuration\AI\Models\Translation\TranslationGptOssConfiguration;
use App\Configuration\AI\Models\Translation\TranslationMistralSmall4Configuration;
use App\Configuration\AI\Models\Translation\TranslationQwen35Configuration;
use App\Contracts\AI\AiModelConfigurationInterface;
use App\Enum\AI\AiModelEnum;
use RuntimeException;

class AiModelConfigurationFactory
{
    public function makeTranslationConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::CLAUDE_SONNET_3_5 => new TranslationClaude35SonnetConfiguration(),
            AiModelEnum::CLAUDE_SONNET_4_5 => new TranslationClaude45SonnetConfiguration(),
            AiModelEnum::CLAUDE_OPUS_4_7 => new TranslationClaude47OpusConfiguration(),
            AiModelEnum::GPT_OSS => new TranslationGptOssConfiguration(),
            AiModelEnum::MISTRAL_SMALL_4 => new TranslationMistralSmall4Configuration(),
            AiModelEnum::QWEN_3_5 => new TranslationQwen35Configuration(),
            AiModelEnum::GPT_5_5 => new TranslationGpt55Configuration(),
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for translation!"),
        };
    }

    public function makeEmbeddingConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::MXBAI_EMBED_LARGE => new EmptyConfiguration(),
            AiModelEnum::EMBEDDING_GEMMA => new EmptyConfiguration(),
            AiModelEnum::MISTRAL_EMBED => new EmptyConfiguration(),
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for embedding!"),
        };
    }

    public function makeClassificationConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::GEMMA_3 => new ClassificationGemma3Configuration(),
            AiModelEnum::CLAUDE_SONNET_3_5 => new ClassificationClaude35Configuration(),
            AiModelEnum::DEEPSEEK_R1 => new ClassificationDeepseekR1Configuration(),
            AiModelEnum::GPT_OSS => new ClassificationGptOssConfiguration(),
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for classification!"),
        };
    }

    public function makeSummarizeConfiguration(AiModelEnum $modelName): AiModelConfigurationInterface
    {
        return match ($modelName) {
            AiModelEnum::GEMMA_3 => new SummarizeGemma3Configuration(),
            AiModelEnum::CLAUDE_SONNET_3_5 => new SummarizeClaude35Configuration(),
            AiModelEnum::GPT_OSS => new SummarizeGptOssConfiguration(),
            default => throw new RuntimeException("Specified model {$modelName->value} does not have configuration for classification!"),
        };
    }
}
