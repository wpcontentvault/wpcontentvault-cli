<?php

declare(strict_types=1);

namespace App\Configuration\AI\Models\Translation;

use App\Contracts\AI\AiModelConfigurationInterface;

class TranslationQwen35Configuration implements AiModelConfigurationInterface
{
    public function getTemperature(): float
    {
        return 0.7;
    }

    public function getTopP(): float
    {
        return 0.8;
    }

    public function getTopK(): float
    {
        return 10.0;
    }

    public function getReasoningEffort(): ?string
    {
        return 'high';
    }

    public function supportsSchema(): bool
    {
        return true;
    }
}
