<?php

declare(strict_types=1);

namespace App\Context\AI\Schema;

class JsonSchema
{
    private FieldsCollection $fields;
    private string $name = 'result';
    private bool $strict = true;

    public function __construct()
    {
        $this->fields = new FieldsCollection();
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addString(string $name, string $description): SchemaField
    {
        return $this->fields->addField('string', $name, $description);
    }

    public function hasFields(): bool
    {
        return $this->fields->count() > 0;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'strict' => $this->strict,
            'schema' => [
                'type' => 'object',
                'properties' => $this->fields->toArray(),
                'required' => $this->fields->getRequiredProperties(),
                'additionalProperties' => false,
            ]
        ];
    }
}
