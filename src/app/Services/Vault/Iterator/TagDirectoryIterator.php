<?php

declare(strict_types=1);

namespace App\Services\Vault\Iterator;

use App\Services\Vault\VaultPathResolver;
use Symfony\Component\Finder\Finder;

class TagDirectoryIterator
{
    public function __construct(
        private VaultPathResolver $pathResolver,
    ) {}

    public function getTagDirectories(): \Generator
    {
        $finder = new Finder();
        $finder->name('*')
            ->notName('.')
            ->notName('..');
        $finder->sortByName();

        $tagsPath = $this->pathResolver->getRoot() . 'tags/';

        foreach ($finder->directories()->in($tagsPath) as $dir) {
            /** @var \SplFileInfo $dir */
            yield $dir;
        }
    }

    public function getTagDirectoryNames(): array
    {
        $dirs = [];

        foreach ($this->getTagDirectories() as $dir) {
            $dirs[] = $dir->getBasename();
        }

        return $dirs;
    }
}
