<?php

declare(strict_types=1);

namespace App\Configuration\AI\Models\Classification;

use App\Contracts\AI\AiModelConfigurationInterface;

class ClassificationGemma3Configuration implements AiModelConfigurationInterface
{
    public function getTemperature(): float
    {
        return 0.1;
    }

    public function getTopP(): float
    {
        return 0.8;
    }

    public function getTopK(): float
    {
        return 2.0;
    }
}
