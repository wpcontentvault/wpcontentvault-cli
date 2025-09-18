<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property string $article_id
 * @property string $image_id
 * @property int $locale_id
 * @property string|null $external_id
 * @property string|null $external_url
 * @property string|null $thumbnail_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Article $article
 * @property-read \App\Models\Image $image
 * @property-read \App\Models\Locale $locale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereExternalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereThumbnailUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImageLocalization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ImageLocalization extends Model
{
    use HasUuids;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id', 'id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
