<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\LocaleOptionsCast;
use App\Context\LocaleOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property LocaleOptions $options
 * @method static \Database\Factories\LocaleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Locale extends Model
{
    use HasFactory;

    public $casts = [
        'options' => LocaleOptionsCast::class,
    ];
}
