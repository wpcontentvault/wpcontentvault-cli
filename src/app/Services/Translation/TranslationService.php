<?php

declare(strict_types=1);

namespace App\Services\Translation;

use App\Context\AI\Chat\ChatMessagesBag;
use App\Context\AI\Chat\ChatItem;
use App\Context\AI\Tools\ToolsCollection;
use App\Context\AI\TranslationResult;
use App\Context\Translation\TranslationHistoryItem;
use App\Context\Translation\TranslationOptions;
use App\Enum\GutenbergBlogTypeEnum;
use App\Exceptions\AiDeserializationException;
use App\Exceptions\AiException;
use App\Exceptions\Translation\TranslationHistoryException;
use App\Models\Article;
use App\Models\Locale;
use App\Models\Paragraph;
use App\Models\ParagraphTranslation;
use App\Registry\AiSettingsRegistry;
use App\Repositories\ParagraphRepository;
use App\Services\AI\OpenAiCompatibleService;
use App\Services\Console\ApplicationOutput;
use App\Services\Database\ArticleTranslationLoader;
use Illuminate\Support\Collection;

class TranslationService
{
    private const TRANSLATABLE_TYPES = [
        GutenbergBlogTypeEnum::PARAGRAPH->value,
        GutenbergBlogTypeEnum::HEADING->value,
        GutenbergBlogTypeEnum::LIST_BLOCK->value,
        GutenbergBlogTypeEnum::TABLE->value,
    ];

    public function __construct(
        private OpenAiCompatibleService  $aiService,
        private ParagraphRepository      $paragraphs,
        private ArticleTranslationLoader $articleTranslationLoader,
        private ApplicationOutput        $applicationOutput,
        private AiSettingsRegistry       $aiSettings,
    ) {}

    public function loadTranslationsFromStorage(Article $article, Locale $locale): void
    {
        $translations = $this->articleTranslationLoader->fetchAllTranslationsFromStorage($article, $locale);

        $paragraphs = $this->paragraphs->findParagraphsByArticle($article)->keyBy('id');

        foreach ($paragraphs as $paragraph) {
            /** @var Paragraph $paragraph */
            if (in_array($paragraph->type, self::TRANSLATABLE_TYPES) === false) {
                continue;
            }

            if (isset($translations[$paragraph->id]) === false) {
                continue;
            }

            $translation = $paragraph->findTranslationByLocale($locale);

            $text = $translations[$paragraph->id];

            if ($translation === null) {
                $this->saveTranslationForParagraph($paragraph, $locale, $text);
            } else {
                $this->updateTranslationForParagraph($paragraph, $translation, $text);
            }
        }
    }

    public function generateMissingTranslationsForArticle(Article $article, Locale $locale): void
    {
        $paragraphs = $this->paragraphs->findParagraphsByArticle($article)->keyBy('id');
        $lastHeading = $this->paragraphs->getLastHeaderForArticle($article);

        foreach ($paragraphs as $paragraph) {
            /** @var Paragraph $paragraph */
            if (in_array($paragraph->type, self::TRANSLATABLE_TYPES) === false) {
                continue;
            }

            $translation = $paragraph->findTranslationByLocale($locale);
            if ($translation !== null && $translation->source_hash === $paragraph->hash) {
                continue;
            }

            if (empty($paragraph->content)) {
                continue;
            }

            $options = new TranslationOptions(
                isHeading: $paragraph->type === GutenbergBlogTypeEnum::HEADING->value,
                isLastHeading: $lastHeading?->getKey() === $paragraph->getKey(),
                context: $article->context,
                history: $this->buildTranslationHistory($paragraphs, $paragraph, $locale),
            );

            $translated = $this->translate($paragraph->content, $article->locale, $locale, $options);

            $this->applicationOutput->info("Paragraph translated, input: {$translated->inputTokens}, output: {$translated->outputTokens}");

            if ($translation === null) {
                $this->saveTranslationForParagraph($paragraph, $locale, $translated->text);
            } else {
                $this->updateTranslationForParagraph($paragraph, $translation, $translated->text);
            }
        }
    }

    private function buildTranslationHistory(Collection $paragraphs, Paragraph $paragraph, Locale $locale): Collection
    {
        $history = collect();
        $list = (clone $paragraphs)->keyBy('order');

        //No history for headings
        if ($paragraph->type === GutenbergBlogTypeEnum::HEADING->value) {
            return $history;
        }

        for ($i = $paragraph->order - 1; $i >= 0; $i--) {

            /** @var Paragraph $sibling */
            $sibling = $list->get($i);
            if (null === $sibling) {
                continue;
            }
            if (in_array($sibling->type, self::TRANSLATABLE_TYPES) === false) {
                continue;
            }

            //re-read translations from database
            $sibling->refresh();

            $translation = $sibling->findTranslationByLocale($locale);
            if (null === $translation) {
                throw new TranslationHistoryException("No translation found for {$locale} for paragraph {$paragraph->id}");
            }

            $history->push(new TranslationHistoryItem(
                original: $sibling->content,
                translation: $translation->content,
            ));

            //Provide history only of last paragraph
            if ($sibling->type === GutenbergBlogTypeEnum::HEADING->value) {
                break;
            }
        }

        return $history;
    }

    private function saveTranslationForParagraph(Paragraph $paragraph, Locale $locale, string $content): void
    {
        $translation = new ParagraphTranslation;
        $translation->article()->associate($paragraph->article);
        $translation->locale()->associate($locale);
        $translation->paragraph()->associate($paragraph);
        $translation->content = $content;
        $translation->source_hash = $paragraph->hash;
        $translation->save();
    }

    private function updateTranslationForParagraph(Paragraph $paragraph, ParagraphTranslation $translation, string $content): void
    {
        $translation->content = $content;
        $translation->source_hash = $paragraph->hash;
        $translation->save();
    }

    private function translate(string $text, Locale $source, Locale $target, TranslationOptions $options): TranslationResult
    {
        $system = <<<SYSTEM
You are artificial intelligence the task of whom is translate technical texts from {$source->name} to {$target->name}.
You have strong experience with Linux operating systems, know a lot about open source software movement.
User provides you paragraph of article which need to be translated.
After translating take a step back, review translation and make sure that all sentences sound fluently on {$target->name} language.
Preserve all markdown markup. That is very important!
Do not add escape characters for config file paths!
Do not decode encoded html entities!

Output final result only as a json object with two fields.
The first field named text with translated text and the second field named comments if you need to add any notices.
SYSTEM;

        if ($options->isHeading) {
            $system .= "\nNote that paragraph you are translating is heading.\n";
        }
        if (empty($target->options->customConsolationsLabel) === false && $options->isLastHeading) {
            $system .= "\nAlways translate consolations header as {$target->options->customConsolationsLabel}.\n";
        }
        if ($target->code === 'en') {
            $system .= "\nNote, that you should use American English.";
        }
        if ($target->options->shouldCapitalizeTitle && $options->isHeading) {
            $system .= "\nUse title case for this sentence.";
        }

        if ($options->context !== null) {
            $system .= "CONTEXT: \n" . $options->context . "\nINPUT:\n";
        }

        $messages = new ChatMessagesBag();
        $messages->setSystemMessage($system);

        foreach ($options->history ?? [] as $item) {
            /** @var TranslationHistoryItem $item */
            $messages->addUserMessage($item->original);
            $messages->addAssistantMessage($item->translation);
        }

        $messages->addUserMessage($text);

        $result = $this->aiService->completions(
            $this->aiSettings->getTranslationConfiguration(),
            $messages,
            new ToolsCollection,
            true
        );

        $content = str_replace('```json', '', $result->content);
        $content = str_replace('```', '', $content);
        $content = str_replace('\_', '_', $content);
        $data = json_decode($content, true);

        if ($data === null) {
            dump($content);
            throw new AiDeserializationException(json_last_error(), json_last_error_msg());
        }

        if (isset($data['text']) === false) {
            throw new AiException('Unexpected AI response! ' . print_r($data, true));
        }

        return new TranslationResult(
            text: $data['text'],
            comments: $data['comments'] ?? '',
            inputTokens: $result->inputTokens,
            outputTokens: $result->outputTokens
        );
    }
}
