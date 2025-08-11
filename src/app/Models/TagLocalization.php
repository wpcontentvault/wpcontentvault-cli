<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $tag_id
 * @property int $locale_id
 * @property int|null $external_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Locale $locale
 * @property-read \App\Models\Tag $tag
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagLocalization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TagLocalization extends Model
{
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'locale_id', 'id');
    }
}
