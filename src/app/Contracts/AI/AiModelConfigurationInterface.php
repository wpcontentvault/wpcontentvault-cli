<?php

declare(strict_types=1);

namespace App\Contracts\AI;

interface AiModelConfigurationInterface
{
    public function getTemperature(): float;

    public function getTopK(): float;

    public function getTopP(): float;
}
