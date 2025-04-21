<?php

declare(strict_types=1);

namespace App\Context\AI\Tools;

use Closure;

class ToolFunction
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $properties,
        public readonly Closure $callable,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallable(): Closure
    {
        return $this->callable;
    }

    public function toArray(): array
    {
        $properties = [];
        $required = [];

        foreach ($this->properties as $property) {
            /** @var ToolProperty $property */
            $properties[$property->getName()] = $property->toArray();

            if ($property->isRequired()) {
                $required[] = $property->getName();
            }
        }

        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name,
                'description' => $this->description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ],
        ];
    }
}
