<?php

declare(strict_types=1);

namespace App\Services\Vault;

use App\Services\Converters\MarkdownToObject\MarkdownToObjectConverter;
use Illuminate\Support\Collection;

class MarkdownLoader
{
    public function __construct(
        private MarkdownToObjectConverter $converter,
    ) {}

    public function loadBlocksFromPath(string $path, string $name): Collection
    {
        $data = file_get_contents($path.'/'.$name.'.md');

        return $this->converter->convert($data);
    }
}
