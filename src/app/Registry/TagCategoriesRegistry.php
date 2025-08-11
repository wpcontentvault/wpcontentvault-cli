<?php

declare(strict_types=1);

namespace App\Registry;

use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;

class TagCategoriesRegistry
{
    public array $categories = [];

    public function __construct() {
        $pathResolver = new VaultPathResolver;
        $loader = new VaultConfigLoader;

        $categories = $loader->loadFromPath($pathResolver->getRoot(), 'tag_categories.json');

        foreach ($categories as $category) {
            $this->categories[$category['slug']] = $category['description'];
        }
    }
}
