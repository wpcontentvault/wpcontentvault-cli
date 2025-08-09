<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Context\AI\Chat\ChatMessagesBag;
use App\Context\AI\ClassificationResult;
use App\Context\AI\Tools\ToolsCollection;
use App\Exceptions\AiDeserializationException;
use App\Exceptions\AiException;
use App\Models\Article;
use App\Models\Category;
use App\Registry\AiSettingsRegistry;
use App\Repositories\ParagraphRepository;
use App\Services\AI\OpenAiCompatibleService;
use Illuminate\Support\Collection;

class ClassificationService
{
    public function __construct(
        private OpenAiCompatibleService $aiService,
        private AiSettingsRegistry      $aiSettings,
        private ParagraphRepository     $paragraphs,
    ) {}

    public function suggestCategoryForArticle(Article $article, Collection $collection): Category
    {
        $collection = $collection->keyBy('slug');

        $title = $article->title;

        $paragraphsList = $this->paragraphs->findParagraphsByArticle($article, 2)
            ->pluck('content')
            ->toArray();

        $wrapUpHeading = $this->paragraphs->getLastHeaderForArticle($article);
        if (null !== $wrapUpHeading) {
            $wrapUpList = $this->paragraphs->getParagraphsAfter($article, $wrapUpHeading, 2)
                ->pluck('content')
                ->toArray();
            $wrapup = implode("\n", $wrapUpList);
        } else {
            $wrapup = "";
        }

        $annotation = implode("\n", $paragraphsList);

        $result = $this->suggestCategory($title, $annotation, $wrapup, $collection);

        if (false === $collection->has($result->content)) {
            throw new \RuntimeException("Can't resolve category: {$result->content}");
        }

        return $collection->get($result->content);
    }

    public function suggestCategory(string $title, string $annotation, string $wrapUp, Collection $categories): ClassificationResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is categorize articles.
You have strong experience with Linux operating systems, know a lot about open source software movement.
User provides you article title and its annotation and wrapping up section.
Y you should choose most suitable category from list below.

Output final result only as a json object with two fields.
The first field named slug with the category slug, and the second field named comments if you need to add any notices.
Output only JSON
SYSTEM;
        $system .= "CATEGORIES:\n";

        foreach ($categories as $category) {
            $system .= $category->slug . ' - ' . $category->description . "\n";
        }

        $system .= "INPUT:\n";

        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        $user = <<<USER
Title: {$title}
Annotation: {$annotation}
Wrapping Up: {$wrapUp}
USER;
        $messages->addUserMessage($user);

        $result = $this->aiService->completions(
            $this->aiSettings->getClassificationConfiguration(),
            $messages,
            new ToolsCollection,
            true
        );

        $data = json_decode($result->content, true);

        if ($data === null) {
            dump($result->content);
            throw new AiDeserializationException(json_last_error(), json_last_error_msg());
        }

        if (isset($data['slug']) === false) {
            throw new AiException('Unexpected AI response! ' . print_r($data, true));
        }

        return new ClassificationResult(
            content: $data['slug'],
            comments: $data['comments'] ?? '',
            inputTokens: $result->inputTokens,
            outputTokens: $result->outputTokens
        );
    }
}
