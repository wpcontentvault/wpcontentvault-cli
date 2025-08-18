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
}
