<?php

declare(strict_types=1);

namespace App\Contracts\AI;

interface AiModelConfigurationInterface
{
    public function getTemperature(): float;
}
