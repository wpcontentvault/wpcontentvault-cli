<?php

declare(strict_types=1);

namespace App\Enum\AI;

enum FinishReason: string
{
    case STOP = 'stop';
    case TOOL_CALLS = 'tool_calls';
    case CONNECTION_EXCEPTION = 'connection_exception';
    case CLIENT_EXCEPTION = 'client_exception';

    case ERROR = 'error';
}
