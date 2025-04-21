<?php

declare(strict_types=1);

namespace App\Contracts\Extension;

use App\Configuration\Extension\ExtensionConfiguration;

interface ExtensionProviderInterface
{
    public function getExtensionConfiguration(): ExtensionConfiguration;
}
