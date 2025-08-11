<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $article_id
 * @property string $paragraph_id
 * @property int $locale_id
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $source_hash
 * @property-read \App\Models\Article $article
 * @property-read \App\Models\Locale $locale
 * @property-read \App\Models\Paragraph $paragraph
 * @method static \Database\Factories\ParagraphTranslationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereParagraphId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereSourceHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ParagraphTranslation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ParagraphTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\ParagraphTranslationFactory> */
    use HasFactory;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function paragraph(): BelongsTo
    {
        return $this->belongsTo(Paragraph::class, 'paragraph_id', 'id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
