<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Configuration\AI\AiRequestConfiguration;
use App\Context\AI\ChatCompletionResult;
use App\Context\AI\Responses\ChatCompletionResponse;
use App\Context\AI\Responses\ToolCall;
use App\Context\AI\Tools\ToolsCollection;
use App\Enum\AI\FinishReason;
use App\Exceptions\AIClientException;
use App\Services\AI\Client\OpenAiCompatibleClient;
use App\Services\AI\Tools\ToolsCaller;
use App\Services\Console\ApplicationOutput;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class OpenAiCompatibleService
{
    private OpenAiCompatibleClient $client;

    private ToolsCaller $caller;

    private ApplicationOutput $output;

    public function __construct(
        OpenAiCompatibleClient $client,
        ApplicationOutput $output,
    ) {
        $this->client = $client;
        $this->caller = new ToolsCaller;

        $this->output = $output;
    }

    public function completions(
        AiRequestConfiguration $aiConfig,
        string $system,
        string $message,
        ToolsCollection $tools,
        bool $json = false
    ): ChatCompletionResult {
        $messages = [
            [
                'role' => 'system',
                'content' => $system,
            ],
            [
                'role' => 'user',
                'content' => $message,
            ],
        ];

        $inputTokens = 0;
        $outputTokens = 0;

        $counter = 0;

        do {
            $counter++;

            if ($counter > 5) {
                throw new RuntimeException('Too many attempts!');
            }

            $response = $this->safeCall(function () use ($aiConfig, $messages, $tools, $json) {
                return $this->client->completions(
                    $aiConfig,
                    $messages,
                    $tools->getArray(),
                    $json
                );
            });

            $inputTokens += $response->promptTokens;
            $outputTokens += $response->completionTokens;

            if ($response->finishReason->value === FinishReason::TOOL_CALLS->value) {
                foreach ($response->toolCalls as $call) {
                    /** @var ToolCall $call */
                    $message = $this->caller->call($tools->getByName($call->function), $call->arguments);

                    $messages[] = [
                        'tool_call_id' => $call->id,
                        'role' => 'tool',
                        'name' => $call->function,
                        'content' => $message,
                    ];
                }
            }

            if($response->finishReason->value === FinishReason::ERROR->value) {
                throw new AiClientException("AI operation error: " . $response->content);
            }
        } while ($response->finishReason->value !== FinishReason::STOP->value);

        return new ChatCompletionResult(
            content: $response->content,
            inputTokens: $inputTokens,
            outputTokens: $outputTokens
        );
    }

    private function safeCall(Closure $callable): ChatCompletionResponse
    {
        try {
            return $callable();
        } catch (AIClientException $exception) {
            $this->output->error('AI request error: '.$exception->getMessage());

            return new ChatCompletionResponse(
                finishReason: FinishReason::CLIENT_EXCEPTION,
                promptTokens: 0,
                completionTokens: 0,
                totalTokens: 0,
                totalTime: 0,
                content: $exception->getMessage(),
                toolCalls: [],
            );
        } catch (ConnectionException $exception) {
            $this->output->error('AI request error: '.$exception->getMessage());

            return new ChatCompletionResponse(
                finishReason: FinishReason::CONNECTION_EXCEPTION,
                promptTokens: 0,
                completionTokens: 0,
                totalTokens: 0,
                totalTime: 0,
                content: $exception->getMessage(),
                toolCalls: [],
            );
        }
    }
}
