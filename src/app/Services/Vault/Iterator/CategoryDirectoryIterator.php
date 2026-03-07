<?php

declare(strict_types=1);

namespace App\Services\Vault\Iterator;

use App\Services\Vault\VaultPathResolver;
use Symfony\Component\Finder\Finder;

class CategoryDirectoryIterator
{
    public function __construct(
        private VaultPathResolver $pathResolver,
    ) {}

    public function getCategoryDirectories(): \Generator
    {
        $finder = new Finder();
        $finder->name('*')
            ->notName('.')
            ->notName('..');
        $finder->sortByName();

        $tagsPath = $this->pathResolver->getRoot() . 'categories/';

        foreach ($finder->directories()->in($tagsPath) as $dir) {
            /** @var \SplFileInfo $dir */
            yield $dir;
        }
    }
}
