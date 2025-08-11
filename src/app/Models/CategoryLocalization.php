<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $category_id
 * @property int $locale_id
 * @property int|null $external_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Locale $locale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryLocalization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryLocalization extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
