<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Registry\AiSettingsRegistry;

class EmbeddingsService
{
    public function __construct(
        private AiSettingsRegistry      $aiSettingsRegistry,
        private OpenAiCompatibleService $service
    ) {}

    public function embeddings(string $text): array
    {
        return $this->service->embeddings(
            $this->aiSettingsRegistry->getEmbeddingConfiguration(),
            $text
        );
    }
}
