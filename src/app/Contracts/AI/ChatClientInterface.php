<?php

declare(strict_types=1);

namespace App\Contracts\AI;

use App\Configuration\AI\AiRequestConfiguration;
use App\Context\AI\Responses\ChatCompletionResponse;

interface ChatClientInterface
{
    public function completions(AiRequestConfiguration $aiConfig, array $messages = [], array $tools = []): ChatCompletionResponse;
}
