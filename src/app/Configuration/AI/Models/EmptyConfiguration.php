<?php

declare(strict_types=1);

namespace App\Configuration\AI\Models;

use App\Contracts\AI\AiModelConfigurationInterface;

class EmptyConfiguration implements AiModelConfigurationInterface
{
    public function getTemperature(): float
    {
        return 0;
    }

    public function getTopP(): float
    {
        return 0;
    }

    public function getTopK(): float
    {
        return 0;
    }

    public function getReasoningEffort(): ?string
    {
        return null;
    }
}
