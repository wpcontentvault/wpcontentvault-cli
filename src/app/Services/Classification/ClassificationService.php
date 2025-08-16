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
use App\Models\Locale;
use App\Models\Paragraph;
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

    public function suggestTagsForArticle(Article $article, Collection $collection, string $description): Collection
    {
        $collection = $collection->keyBy('slug');

        $result = $this->suggestTag($this->fetchArticleContent($article), $collection, $description);

        $suggestedTags = collect();

        if ($result->content === 'none') {
            return $suggestedTags;
        }

        $tagSlugs = explode(',', $result->content);

        foreach ($tagSlugs as $tagSlug) {
            $tagSlug = trim($tagSlug);
            if (false === $collection->has($tagSlug)) {
                continue;
                throw new \RuntimeException("Can't resolve tag: {$tagSlug}, description: {$description}, Comments: {$result->comments}");
            }

            $suggestedTags = $suggestedTags->add($collection->get($tagSlug));
        }

        return $suggestedTags;
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
You are an AI specialized in accurately assigning tags to articles about Linux operating systems and open source software.

## Your Task
Assign tags from a provided list to articles based on their title, annotation, and content summary. You must be highly selective and only assign tags that genuinely match the article's content.

## Critical Rules
1. **Only use tags from the provided list** - Never create or suggest new tags
2. **Match content precisely** - A tag must directly relate to the article's main topics or technologies discussed
3. **Be conservative** - When in doubt, don't assign a tag. It's better to assign fewer accurate tags than many loosely related ones

## Process
1. **Read carefully**: Analyze the article title, annotation, and summary thoroughly
2. **Identify core topics**: What are the 2-3 main subjects discussed?
3. **Match against list**: Check if any provided tags directly correspond to these core topics
4. **Validate relevance**: For each potential tag, ask: "Is this tag central to what this article discusses?"
5. **Final review**: Ensure each selected tag is mentioned or clearly implied in the provided content

## Output Format
Respond only with valid JSON:
```json
{
  "slugs": "tag1,tag2,tag3" OR "none",
  "comments": "Brief explanation of your reasoning or 'none' if no clarification needed"
}
SYSTEM;
        $system .= "\nCATEGORY: " . $description . "\n";

        $system .= "TAGS:\n";

        foreach ($tags as $tag) {
            $system .= $tag->slug . ' - ' . $tag->description . "\n";
        }

        $system .= "INPUT:\n";

        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        $user = <<<USER
Title: {$content->title}
Annotation: {$content->annotation}
Wrapping Up: {$content->warpingUp}
Table Of Contents:
{$content->tableOfContent}
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

        if (isset($data['slugs']) === false) {
            throw new AiException('Unexpected AI response! ' . print_r($data, true));
        }

        return new ClassificationResult(
            content: $data['slugs'],
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

        $tableOfContents = $this->paragraphs->getAllHeadingsForArticle($article)
            ->pluck('content')
            ->toArray();

        $annotation = implode("\n", $paragraphsList);

        return new ArticleContent(
            title: $title,
            annotation: $annotation,
            warpingUp: $wrapup,
            tableOfContent: implode("\n", $tableOfContents)
        );
    }
}
