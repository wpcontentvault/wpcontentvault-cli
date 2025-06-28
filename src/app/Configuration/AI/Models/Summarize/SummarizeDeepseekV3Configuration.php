<?php

declare(strict_types=1);

namespace App\Configuration\AI\Models\Summarize;

use App\Contracts\AI\AiModelConfigurationInterface;

class SummarizeDeepseekV3Configuration implements AiModelConfigurationInterface
{
    public function getTemperature(): float
    {
        return 0.4;
    }

    public function getTopP(): float
    {
        return -1;
    }

    public function getTopK(): float
    {
        return 3.0;
    }
}
