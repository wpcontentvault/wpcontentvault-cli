<?php

namespace App\Services\Summarization;

use App\Context\AI\Chat\ChatMessagesBag;
use App\Context\AI\SelectCategoryResult;
use App\Context\AI\SummarizationResult;
use App\Context\AI\Tools\ToolsCollection;
use App\Context\Classification\ArticleContent;
use App\Exceptions\AiDeserializationException;
use App\Exceptions\AiException;
use App\Registry\AiSettingsRegistry;
use App\Services\AI\OpenAiCompatibleService;
use Illuminate\Support\Collection;

class SummarizationService
{
    public function __construct(
        private OpenAiCompatibleService $aiService,
        private AiSettingsRegistry      $aiSettings,
    ) {}

    public function summarizeArticle(string $text): SummarizationResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is summarizing articles.
User provides you the whole article.
You should read and analyze it, then provide summary in 3-4 paragraphs.
Try to provide following information:

- What the main topic of the article
- Key concepts or terminology
- What distribution or operating system is article for
- Article type and tone

Output only the summary in English language.
INPUT:
SYSTEM;
        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        $messages->addUserMessage($text);

        $result = $this->aiService->completions(
            $this->aiSettings->getSummarizeConfiguration(),
            $messages,
            new ToolsCollection,
            false
        );

        dump($result->reasoning);
        return new SummarizationResult(
            summary: $result->content,
            inputTokens: $result->inputTokens,
            outputTokens: $result->outputTokens
        );
    }
}
