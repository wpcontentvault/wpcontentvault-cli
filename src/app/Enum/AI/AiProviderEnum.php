<?php

declare(strict_types=1);

namespace App\Enum\AI;

enum AiProviderEnum: string
{
    case OPEN_ROUTER = 'open_router';
    case OLLAMA = 'ollama';
    case GROQ = 'groq';
    case CLI_PROXY_API = 'cli_proxy_api';
}
