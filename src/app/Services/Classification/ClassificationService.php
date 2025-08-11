<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Context\AI\Chat\ChatMessagesBag;
use App\Context\AI\ClassificationResult;
use App\Context\AI\Tools\ToolsCollection;
use App\Context\Classification\ArticleContent;
use App\Exceptions\AiDeserializationException;
use App\Exceptions\AiException;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
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

        $result = $this->suggestCategory($this->fetchArticleContent($article), $collection);

        if (false === $collection->has($result->content)) {
            throw new \RuntimeException("Can't resolve category: {$result->content}");
        }

        return $collection->get($result->content);
    }

    public function suggestTagForArticle(Article $article, Collection $collection, string $description): ?Tag
    {
        $collection = $collection->keyBy('slug');

        $result = $this->suggestTag($this->fetchArticleContent($article), $collection, $description);

        if ($result->content === 'none') {
            return null;
        }

        if (false === $collection->has($result->content)) {
            throw new \RuntimeException("Can't resolve tag: {$result->content}, description: {$description}, Comments: {$result->comments}");
        }

        return $collection->get($result->content);
    }

    public function suggestCategory(ArticleContent $content, Collection $categories): ClassificationResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is categorize articles.
You have strong experience with Linux operating systems, know a lot about open source software movement.
User provides you article title and its annotation and wrapping up section.
You should choose most suitable category from list below.

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
Title: {$content->title}
Annotation: {$content->annotation}
Wrapping Up: {$content->warpingUp}
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

    public function suggestTag(ArticleContent $content, Collection $tags, string $description): ClassificationResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is assign tags for articles.
You have strong experience with Linux operating systems, know a lot about open source software movement.
User provides you article title and its annotation and wrapping up section.
You should choose most suitable tag from list below.
Choose tag only between tags provided in the list, do not create your own tags if they not listed.
If none of the provided tags suitable, return "none".
After choosing tag, take step back and think does it comply with focus question.

Output final result only as a json object with two fields.
The first field named slug with the tag slug, and the second field named comments if you need to add any notices.
Output only JSON
SYSTEM;
        $system .= "\nFOCUS QUESTION: " . $description . "\n";

        $system .= "TAGS:\n";

        foreach ($tags as $tag) {
            $system .= $tag->slug . "\n";
        }

        $system .= "INPUT:\n";

        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        $user = <<<USER
Title: {$content->title}
Annotation: {$content->annotation}
Wrapping Up: {$content->warpingUp}
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

    private function fetchArticleContent(Article $article): ArticleContent
    {
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

        return new ArticleContent(
            title: $title,
            annotation: $annotation,
            warpingUp: $wrapup,
        );
    }
}
