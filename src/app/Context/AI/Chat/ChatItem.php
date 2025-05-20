<?php

declare(strict_types=1);

namespace App\Context\AI\Chat;

use App\Enum\AI\RoleEnum;

class ChatItem
{
    public function __construct(
        public readonly string   $message,
        public readonly RoleEnum $role,
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->message,
        ];
    }
}
