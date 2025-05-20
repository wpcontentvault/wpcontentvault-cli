<?php

declare(strict_types=1);

namespace App\Context\AI\Chat;

use App\Enum\AI\RoleEnum;

class ChatMessagesBag
{
    private string $systemMessage = "";
    private array $messages = [];

    public function __construct() {}

    public function setSystemMessage(string $message): void
    {
        $this->systemMessage = $message;
    }

    public function addUserMessage(string $message): void
    {
        $this->messages[] = new ChatItem($message, RoleEnum::USER);
    }

    public function addAssistantMessage(string $message): void
    {
        $this->messages[] = new ChatItem($message, RoleEnum::ASSISTANT);
    }

    public function toArray(): array
    {
        array_unshift(
            $this->messages,
            new ChatItem($this->systemMessage, RoleEnum::SYSTEM)
        );

        return collect($this->messages)
            ->map(fn($item) => $item->toArray())
            ->toArray();
    }
}
