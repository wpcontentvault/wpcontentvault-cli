<?php

declare(strict_types=1);

namespace App\Context\AI\Schema;

use App\Contracts\AI\AiModelConfigurationInterface;

class ResponseFormat
{
    private bool $shouldBeJson = false;

    private JsonSchema $schema;

    public function __construct()
    {
        $this->schema = new JsonSchema();
    }

    public function schema(): JsonSchema
    {
        return $this->schema;
    }

    public function json(bool $flag = true): self
    {
        $this->shouldBeJson = $flag;

        return $this;
    }

    public function toArray(AiModelConfigurationInterface $configuration): array
    {
        if (false === $this->shouldBeJson) {
            return [];
        }

        if ($this->schema->hasFields() === 0) {
            return [
                'response_format' => [
                    'type' => 'json_object'
                ]
            ];
        }

        if ($configuration->supportsSchema() === false) {
            return [
                'response_format' => [
                    'type' => 'json_object'
                ]
            ];
        }

        return [
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $this->schema->toArray(),
            ]
        ];
    }
}
