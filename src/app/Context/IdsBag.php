<?php

declare(strict_types=1);

namespace App\Context;

class IdsBag
{
    private array $ids = [];

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function clear(): void
    {
        $this->ids = [];
    }

    public function toArray(): array
    {
        return $this->ids;
    }

    public function add(string $id): void
    {
        if (! in_array($id, $this->ids)) {
            $this->ids[] = $id;
        }
    }
}
