<?php

declare(strict_types=1);

namespace App\Configuration\AI\Models\Translation;

use App\Contracts\AI\AiModelConfigurationInterface;

class TranslationClaude35SonnetConfiguration implements AiModelConfigurationInterface
{
    public function getTemperature(): float
    {
        return 0.4;
    }

    public function getTopP(): float
    {
        return 0.3;
    }

    public function getTopK(): float
    {
        return 5.0;
    }
}
