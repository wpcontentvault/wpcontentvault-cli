<?php

declare(strict_types=1);

namespace App\Services\AI\Client;

use App\Configuration\AI\AiRequestConfiguration;
use App\Context\AI\Responses\ChatCompletionResponse;
use App\Context\AI\Responses\ToolCall;
use App\Contracts\AI\AiProviderConfigurationInterface;
use App\Contracts\AI\ChatClientInterface;
use App\Enum\AI\FinishReason;
use App\Exceptions\AIClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenAiCompatibleClient implements ChatClientInterface
{
    public function __construct() {}

    public function completions(
        AiRequestConfiguration $aiConfig, array $messages = [], array $tools = [], bool $json = false): ChatCompletionResponse
    {
        $params = [
            'model' => $aiConfig->getProviderConfiguration()->getModelName($aiConfig->getModel()),
            'messages' => $messages,
            'temperature' => $aiConfig->getModelConfiguration()->getTemperature(),
        ];

        if (empty($tools) === false) {
            $params['tools'] = $tools;
        }

        if ($json) {
            $params['response_format'] = [
                'type' => 'json_object',
            ];
        }

        $response = $this->prepareRequest($aiConfig->getProviderConfiguration())
            ->post('chat/completions', $params);

        if ($response->successful() === false) {
            throw new AIClientException($response->body());
        }

        $data = $response->json();

        $toolCalls = [];

        foreach ($data['choices'][0]['message']['tool_calls'] ?? [] as $call) {
            $toolCalls[] = new ToolCall(
                id: $call['id'],
                function: $call['function']['named'],
                arguments: $call['function']['arguments'],
            );
        }

        if (isset($data['choices']) === false) {
            throw new AIClientException(json_encode($data));
        }

        return new ChatCompletionResponse(
            finishReason: FinishReason::from($data['choices'][0]['finish_reason']),
            promptTokens: $data['usage']['prompt_tokens'],
            completionTokens: $data['usage']['completion_tokens'],
            totalTokens: $data['usage']['total_tokens'],
            content: $data['choices'][0]['message']['content'] ?? null,
            toolCalls: $toolCalls,
        );
    }

    private function prepareRequest(AiProviderConfigurationInterface $configuration): PendingRequest
    {
        return Http::baseUrl($configuration->getBaseUrl())
            ->timeout(240)
            ->connectTimeout(30)
            ->withHeader('Authorization', 'Bearer '.$configuration->getAuthToken());
    }
}
