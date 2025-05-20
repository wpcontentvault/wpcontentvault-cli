<?php

namespace App\Enum\AI;

enum RoleEnum: string
{
    case ASSISTANT = 'assistant';
    case USER = 'user';
    case SYSTEM = 'system';
}
