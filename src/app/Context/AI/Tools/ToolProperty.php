<?php

declare(strict_types=1);

namespace App\Context\AI\Tools;

class ToolProperty
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $description,
        public readonly bool $isRequired = false,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
