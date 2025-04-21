<?php

declare(strict_types=1);

namespace App\Services\Importing;

use App\Services\Vault\VaultPathResolver;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\FullPostData;

class PathResolver
{
    public function __construct(
        private VaultPathResolver $vaultPathResolver,
    ) {}

    public function resolvePathFromPostData(FullPostData $postData): string
    {
        $postId = $postData->id;
        $name = str_replace('/', ' ', $postData->title);

        return $this->vaultPathResolver->resolveArticlePath($postId.'. '.$name);
    }
}
