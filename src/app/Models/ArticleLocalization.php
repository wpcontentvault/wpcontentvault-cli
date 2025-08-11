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
 * @property int $locale_id
 * @property bool $is_original
 * @property int $external_id
 * @property string $url
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Article $article
 * @property-read \App\Models\Locale $locale
 * @method static \Database\Factories\ArticleLocalizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereIsOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLocalization whereUrl($value)
 * @mixin \Eloquent
 */
class ArticleLocalization extends Model
{
    /** @use HasFactory<\Database\Factories\ParagraphTranslationFactory> */
    use HasFactory;

    public $casts = [
        'is_original' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
