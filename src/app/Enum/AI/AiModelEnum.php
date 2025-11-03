<?php

declare(strict_types=1);

namespace App\Enum\AI;

enum AiModelEnum: string
{
    case CLAUSE_SONNET_3_5 = 'claude_sonnet_3_5';
    case DEEPSEEK_V3 = 'deepseek_v3';
    case MXBAI_EMBED_LARGE = 'mxbai_embed_large';
    case GEMMA_3 = 'gemma_3';
    case DEEPSEEK_R1 = 'deepseek_r1';
    case GPT_OSS = 'gpt_oss';

    public function getSafeContentLength(): int
    {
        return match ($this) {
            self::CLAUSE_SONNET_3_5 => 16_000,
            self::DEEPSEEK_V3 => 16_000,
            self::DEEPSEEK_R1 => 16_000,
            self::GEMMA_3 => 16_000,
            self::MXBAI_EMBED_LARGE => 5000,
            self::GPT_OSS => 16_000,
        };
    }
}
