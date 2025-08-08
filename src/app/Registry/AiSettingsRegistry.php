<?php

declare(strict_types=1);

namespace App\Registry;

use App\Configuration\AI\AiRequestConfiguration;
use App\Configuration\AI\Provider\OllamaProviderConfiguration;
use App\Configuration\AI\Provider\OpenRouterProviderConfiguration;
use App\Enum\AI\AiModelEnum;
use App\Enum\AI\AiProviderEnum;
use App\Factories\Ai\AiModelConfigurationFactory;
use App\Services\Vault\VaultConfigLoader;
use App\Services\Vault\VaultPathResolver;
use RuntimeException;

class AiSettingsRegistry
{
    private array $providers = [];

    private AiRequestConfiguration $translationConfiguration;

    private AiRequestConfiguration $embeddingConfiguration;

    private AiRequestConfiguration $classificationConfiguration;

    public function __construct()
    {
        $pathResolver = new VaultPathResolver;
        $loader = new VaultConfigLoader;
        $modelConfFactory = new AiModelConfigurationFactory;

        $aiConfig = $loader->loadFromPath($pathResolver->getRoot(), 'ai.json');

        foreach ($aiConfig['providers'] as $name => $provider) {
            $this->providers[$name] = match ($name) {
                AiProviderEnum::OPEN_ROUTER->value => new OpenRouterProviderConfiguration($provider),
                AiProviderEnum::OLLAMA->value => new OllamaProviderConfiguration($provider),
                default => throw new RuntimeException(sprintf('Provider "%s" does not exist.', $name))
            };
        }

        $translationProvider = $this->providers[AiProviderEnum::from($aiConfig['settings']['translation']['provider'])->value];
        $translationModelName = AiModelEnum::from($aiConfig['settings']['translation']['model']);
        if (empty($translationProvider->getModelName($translationModelName))) {
            throw new RuntimeException("Specified translation provider does not support model {$translationModelName->value}.");
        }

        $this->translationConfiguration = new AiRequestConfiguration(
            $translationProvider,
            $translationModelName,
            $modelConfFactory->makeTranslationConfiguration($translationModelName),
        );

        $embeddingProvider = $this->providers[AiProviderEnum::from($aiConfig['settings']['embeddings']['provider'])->value];
        $embeddingModelName = AiModelEnum::from($aiConfig['settings']['embeddings']['model']);
        if (empty($embeddingProvider->getModelName($embeddingModelName))) {
            throw new RuntimeException("Specified provider does not support model {$embeddingModelName->value}.");
        }

        $this->embeddingConfiguration = new AiRequestConfiguration(
            $embeddingProvider,
            $embeddingModelName,
            $modelConfFactory->makeEmbeddingConfiguration($embeddingModelName),
        );

        $classificationProvider = $this->providers[AiProviderEnum::from($aiConfig['settings']['classification']['provider'])->value];
        $classificationModelName = AiModelEnum::from($aiConfig['settings']['classification']['model']);
        if (empty($classificationProvider->getModelName($classificationModelName))) {
            throw new RuntimeException("Specified provider does not support model {$classificationProvider->value}.");
        }

        $this->classificationConfiguration = new AiRequestConfiguration(
            $classificationProvider,
            $classificationModelName,
            $modelConfFactory->makeClassificationConfiguration($classificationModelName),
        );
    }

    public function getTranslationConfiguration(): AiRequestConfiguration
    {
        return $this->translationConfiguration;
    }

    public function getEmbeddingConfiguration(): AiRequestConfiguration
    {
        return $this->embeddingConfiguration;
    }

    public function getSummarizeConfiguration(): AiRequestConfiguration
    {
        throw new RuntimeException('Not implemented yet.');
    }
}
