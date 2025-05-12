<?php

declare(strict_types=1);

namespace App\Blocks;

use Illuminate\Support\Collection;

abstract class ObjectBlock
{
    private Collection $children;

    private array $attributes = [];

    private ?string $value;

    private ?string $preserveId = null;

    public function __construct(
        array $attributes,
        Collection $children,
        ?string $value = null,
        ?string $preserveId = null
    ) {
        $this->attributes = $attributes;
        $this->children = $children;
        $this->value = $value;
        $this->preserveId = $preserveId;
    }

    abstract public function getType(): string;

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContent(): ?string
    {
        return $this->value;
    }

    public function getRenderedContent(): string
    {
        if (empty($this->value) === false) {
            return $this->value;
        }

        $rendered = '';
        foreach ($this->children as $child) {
            /** @var ObjectBlock $child */
            $rendered .= $child->getRenderedContent();
        }

        return $rendered;
    }

    public function addAttribute(string $name, ?string $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getPreserveId(): string
    {
        return $this->preserveId;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'attrs' => $this->attributes,
            'value' => $this->value,
        ];
    }
}
