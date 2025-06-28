<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryRecord extends Model
{
    /** @use HasFactory<\Database\Factories\DictionaryRecordFactory> */
    use HasFactory;

    public $casts = [
        'embedding' => 'array',
    ];

    public function sourceLocale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'source_id', 'id');
    }

    public function targetLocale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'target_id', 'id');
    }
}
