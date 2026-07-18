<?php

declare(strict_types=1);

namespace App\Enum\AI;

enum AiModelEnum: string
{
    case CLAUDE_SONNET_3_5 = 'claude_sonnet_3_5';
    case CLAUDE_SONNET_4_5 = 'claude_sonnet_4_5';
    case CLAUDE_OPUS_4_7 = 'claude_opus_4_7';
    case DEEPSEEK_V3 = 'deepseek_v3';
    case MISTRAL_SMALL_4 = 'mistral_small_4';
    case MXBAI_EMBED_LARGE = 'mxbai_embed_large';
    case EMBEDDING_GEMMA = 'embedding_gemma';
    case MISTRAL_EMBED = 'mistral_embed_2312';
    case GEMMA_3 = 'gemma_3';
    case DEEPSEEK_R1 = 'deepseek_r1';
    case GPT_OSS = 'gpt_oss';
    case GPT_5_5 = 'gpt_5_5';
    case QWEN_3_5 = 'qwen_3_5';

    public function getSafeContentLength(): int
    {
        return match ($this) {
            self::CLAUDE_SONNET_3_5 => 16_000,
            self::CLAUDE_SONNET_4_5 => 32_000,
            self::CLAUDE_OPUS_4_7 => 32_000,
            self::MISTRAL_SMALL_4 => 16_000,
            self::DEEPSEEK_V3 => 16_000,
            self::DEEPSEEK_R1 => 16_000,
            self::GEMMA_3 => 16_000,
            self::MXBAI_EMBED_LARGE => 500,
            self::EMBEDDING_GEMMA => 2000,
            self::GPT_OSS => 16_000,
            self::QWEN_3_5 => 16_000,
            self::GPT_5_5 => 32_000,
        };
    }
}
