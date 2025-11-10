# AI Configuration Framework RFC

## Overview

This document describes a flexible and extensible AI configuration framework that enables the use of multiple AI providers and models with different settings, along with comprehensive tool support.

## Architecture

### Core Components

#### 1. Provider Configuration System

The framework supports multiple AI providers through a unified interface:

```php
interface AiProviderConfigurationInterface
{
    public function getModelName(AiModelEnum $model): string;
    public function getBaseUrl(): string;
    public function getEmbeddingsUrl(): string;
    public function getAuthToken(): string;
}
```

**Supported Providers:**
- **OpenRouter**: Claude 3.5 Sonnet, Deepseek V3, GPT-OSS
- **Groq**: GPT-OSS
- **Ollama**: MXBAI Embed Large, Gemma 3, Deepseek R1

Each provider implements specific model mappings and authentication mechanisms.

#### 2. Model Configuration System

Models are configured through enums with specific parameters:

```php
enum AiModelEnum: string
{
    case CLAUSE_SONNET_3_5 = 'claude_sonnet_3_5';
    case DEEPSEEK_V3 = 'deepseek_v3';
    case MXBAI_EMBED_LARGE = 'mxbai_embed_large';
    case GEMMA_3 = 'gemma_3';
    case DEEPSEEK_R1 = 'deepseek_r1';
    case GPT_OSS = 'gpt_oss';
}
```

Each model includes:
- Safe content length limits (typically 16,000 characters)
- Provider-specific model names
- Task-specific configurations

#### 3. Task-Based Configuration System

Different AI tasks have optimized configurations:

**Classification Tasks:**
- Temperature: 0.1 (low creativity)
- TopP: 0.8
- TopK: 2.0

**Summarization Tasks:**
- Temperature: 0.1 (low creativity)
- TopP: 0.8
- TopK: 2.0

**Translation Tasks:**
- Temperature: 0.4 (moderate creativity)
- TopP: 0.3
- TopK: 5.0

#### 4. Tools/Functions Support

Comprehensive function calling system:

```php
class ToolFunction
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $properties,
        public readonly Closure $callable,
    ) {}
}
```

**Features:**
- Function definition with parameters
- Parameter validation through ToolProperty
- Automatic tool execution via ToolsCaller
- Support for required/optional parameters

#### 5. Configuration Factory

Factory pattern for creating task-specific configurations:

```php
class AiModelConfigurationFactory
{
    public function makeTranslationConfiguration(AiModelEnum $model): AiModelConfigurationInterface;
    public function makeEmbeddingConfiguration(AiModelEnum $model): AiModelConfigurationInterface;
    public function makeClassificationConfiguration(AiModelEnum $model): AiModelConfigurationInterface;
    public function makeSummarizeConfiguration(AiModelEnum $model): AiModelConfigurationInterface;
}
```

#### 6. Central Registry

`AiSettingsRegistry` manages all AI configurations:

- Loads configuration from JSON file
- Initializes providers dynamically
- Validates model-provider compatibility
- Provides access to task-specific configurations

### Configuration Structure

```json
{
  "providers": {
    "open_router": {
      "access_token": "your-token"
    },
    "ollama": {
      "base_url": "http://localhost:11434"
    },
    "groq": {
      "access_token": "your-token"
    }
  },
  "settings": {
    "translation": {
      "provider": "open_router",
      "model": "claude_sonnet_3_5"
    },
    "embeddings": {
      "provider": "ollama",
      "model": "mxbai_embed_large"
    },
    "classification": {
      "provider": "open_router",
      "model": "claude_sonnet_3_5"
    },
    "summarize": {
      "provider": "open_router",
      "model": "claude_sonnet_3_5"
    }
  }
}
```

### Service Integration

#### OpenAI Compatible Service

The main service handles AI interactions:

```php
class OpenAiCompatibleService
{
    public function embeddings(AiRequestConfiguration $aiConfig, string $text): array;
    public function completions(
        AiRequestConfiguration $aiConfig,
        ChatMessagesBag $messagesBag,
        ToolsCollection $tools,
        bool $json = false
    ): ChatCompletionResult;
}
```

**Features:**
- Automatic retry logic for tool calls
- Content length validation
- Error handling with specific exception types
- Token counting and cost tracking

#### Error Handling

Comprehensive error handling with specific types:

- `AIClientException`: API-level errors
- `AiException`: General AI errors  
- `ConnectionException`: Network issues
- `AiDeserializationException`: Response parsing errors

### Extensibility

#### Adding New Providers

1. Implement `AiProviderConfigurationInterface`
2. Add provider to `AiProviderEnum`
3. Update `AiSettingsRegistry` initialization
4. Configure in JSON file

#### Adding New Models

1. Add model to `AiModelEnum`
2. Implement model-specific configurations
3. Update provider mappings
4. Set safe content length

#### Adding New Tasks

1. Create configuration classes in appropriate namespace
2. Add factory method in `AiModelConfigurationFactory`
3. Update `AiSettingsRegistry` with new configuration
4. Add JSON configuration section

### Security Considerations

- Content length limits prevent token overflow
- Input validation for all parameters
- Secure token handling
- Error message sanitization
- Rate limiting through retry logic

### Performance Optimizations

- Factory pattern for object reuse
- Lazy loading of configurations
- Efficient token counting
- Minimal memory footprint
- Connection pooling support

## Implementation Guidelines

### Best Practices

1. **Use strict types** for all AI-related classes
2. **Validate configurations** at startup
3. **Handle errors gracefully** with specific exceptions
4. **Monitor token usage** for cost control
5. **Test with different providers** for compatibility

### Migration Path

1. Define provider interfaces
2. Implement existing providers
3. Create configuration factory
4. Set up central registry
5. Migrate existing AI code
6. Add comprehensive tests

### Testing Strategy

- Unit tests for each configuration class
- Integration tests with real providers
- Mock providers for CI/CD
- Error scenario testing
- Performance benchmarking

## Conclusion

This framework provides a robust, extensible foundation for AI integration that supports multiple providers, models, and tasks while maintaining clean architecture and comprehensive tool support.