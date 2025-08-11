<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

/**
 * 
 *
 * @property string $id
 * @property string $article_id
 * @property string $hash
 * @property string $type
 * @property string|null $content
 * @property int|null $order
 * @property bool $is_stale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Article $article
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ParagraphTranslation> $translations
 * @property-read int|null $translations_count
 * @method static \Database\Factories\ParagraphFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereIsStale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paragraph whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Paragraph extends Model
{
    /** @use HasFactory<\Database\Factories\ParagraphFactory> */
    use HasFactory;

    use HasUuids;

    public $casts = [
        'is_stale' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ParagraphTranslation::class, 'paragraph_id', 'id');
    }

    public function findTranslationByLocale(Locale $locale): ?ParagraphTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->locale_id == $locale->getKey()) {
                return $translation;
            }
        }

        return null;
    }

    public function getContent(?Locale $locale = null, bool $throw = false): ?string
    {
        if (empty($this->content)) {
            return '';
        }

        if ($locale === null) {
            return $this->content;
        }

        $translation = $this->findTranslationByLocale($locale);

        if ($translation !== null) {
            return $translation->content;
        } elseif ($throw) {
            throw new RuntimeException("No translation found for locale $locale");
        }

        return $this->content;
    }
}
