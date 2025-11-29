<?php

declare(strict_types=1);

namespace App\Context\AI\Schema;

class SchemaField
{
    private string $name;
    private string $description;
    private string $type;
    private bool $required = true;

    public function __construct(string $type, string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
    }

    public function nullable(): SchemaField
    {
        $this->required = false;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
