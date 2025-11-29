<?php

declare(strict_types=1);

namespace App\Context\AI\Schema;

class FieldsCollection
{
    private array $fields = [];

    public function addField($type, $name, $description): SchemaField
    {
        $field = new SchemaField($type, $name, $description);
        $this->fields[] = $field;

        return $field;
    }

    public function count(): int
    {
        return count($this->fields);
    }

    public function getRequiredProperties(): array
    {
        $required = [];

        foreach ($this->fields as $field) {
            if ($field->isRequired()) {
                $required[] = $field->getName();
            }
        }

        return $required;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->fields as $field) {
            $result[$field->getName()] = $field->toArray();
        }

        return $result;
    }
}
