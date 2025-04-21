<?php

declare(strict_types=1);

namespace App\Context\AI\Tools;

use RuntimeException;

class ToolsCollection
{
    private array $tools = [];

    public function add(ToolFunction $function): void
    {
        $this->tools[$function->getName()] = $function;
    }

    public function getByName(string $name): ToolFunction
    {
        if (isset($this->tools[$name]) === false) {
            throw new RuntimeException("Function with name $name not found!");
        }

        return $this->tools[$name];
    }

    public function getArray(): array
    {
        $tools = [];
        foreach ($this->tools as $tool) {
            $tools[] = $tool->toArray();
        }

        return $tools;
    }
}
