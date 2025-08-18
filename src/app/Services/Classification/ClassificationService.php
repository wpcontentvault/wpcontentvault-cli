<?php

declare(strict_types=1);

namespace App\Services\Classification;

use App\Context\AI\Chat\ChatMessagesBag;
use App\Context\AI\SelectCategoryResult;
use App\Context\AI\SelectTagsResult;
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

    public function suggestTagsForArticle(Article $article, string $tagsList): array
    {
        $result = $this->suggestTag($this->fetchArticleContent($article), $tagsList);

        $categories = [];
        foreach ($result->tags as $category => $tags) {
            if ($tags === 'none') {
                continue;
            }

            if (false === is_array($tags)) {
                $tags = explode(',', $tags);
            }
            $categories[$category] = array_map(function ($tagSlug) {
                return trim($tagSlug);
            }, $tags);
        }

        return $categories;
    }

    public function suggestCategory(ArticleContent $content, Collection $categories): SelectCategoryResult
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

        return new SelectCategoryResult(
            content: $data['slug'],
            comments: $data['comments'] ?? '',
            inputTokens: $result->inputTokens,
            outputTokens: $result->outputTokens
        );
    }

    public function suggestTag(ArticleContent $content, string $tagsList): SelectTagsResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is assign tags for articles.
You have strong experience with Linux operating systems, know a lot about open source software movement.
The tag assigning process is split by steps. You will choose tags only for a specific tag category.
User provides you tags list, article title, annotation table of contents and wrapping up section.
You can choose none, one or multiple tags suitable for article from the provided list.
Analyze provided parts of article and think which tags from the list are most suitable.
Repeat that for each category.
If none of the provided tags suitable, return "none".
Tags in the list split by the category for better convenience.
Try to find suitable tag for each tag category.

Output final result only as a json object with three fields.
The first field named explanation which contains explanation why you have chosen those tags for each category.
The second field named slugs with the tag slugs.
The third field named comments if you need to add any notices.

The explanation field should contain an array where key is tag category and value is explanation
The slugs field should contain an array where the key is tag category and values tag slugs related to that category separated by comma.
Output only JSON
SYSTEM;

        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        $tags = "\nTAGS:\n";
        $tags .= $tagsList;
        $messages->addUserMessage($tags);

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
        dump($data);
        if ($data === null) {
            dump($result->content);
            throw new AiDeserializationException(json_last_error(), json_last_error_msg());
        }

        if (isset($data['slugs']) === false || false === is_array($data['slugs'])) {
            throw new AiException('Unexpected AI response! ' . print_r($data, true));
        }

        return new SelectTagsResult(
            tags: $data['slugs'],
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
