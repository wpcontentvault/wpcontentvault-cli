<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\IdsBagCast;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $path
 * @property array<array-key, mixed>|null $content
 * @property \App\Context\IdsBag $image_ids
 * @property string|null $author
 * @property string|null $title
 * @property string|null $thumbnail_url
 * @property string|null $url
 * @property int $locale_id
 * @property int|null $external_id
 * @property CarbonImmutable|null $published_at
 * @property CarbonImmutable|null $modified_at
 * @property array<array-key, mixed>|null $keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Context\IdsBag $paragraph_ids
 * @property string|null $context
 * @property-read \App\Models\Locale $locale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArticleLocalization> $localizations
 * @property-read int|null $localizations_count
 *
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereImageIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereModifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereParagraphIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereThumbnailUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;
    use HasUuids;

    public $casts = [
        'content' => 'array',
        'keywords' => 'array',
        'image_ids' => IdsBagCast::class,
        'paragraph_ids' => IdsBagCast::class,
        'published_at' => 'immutable_datetime',
        'modified_at' => 'immutable_datetime',
    ];

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }

    public function localizations(): HasMany
    {
        return $this->hasMany(ArticleLocalization::class, 'article_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'article_id', 'id');
    }

    public function findLocalizationByLocale(Locale $locale): ?ArticleLocalization
    {
        foreach ($this->localizations as $localization) {
            if ($localization->locale_id == $locale->getKey()) {
                return $localization;
            }
        }

        return null;
    }
}
