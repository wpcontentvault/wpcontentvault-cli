<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagCategory extends Model
{
    use HasFactory;
    use HasUuids;

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'category_id', 'id');
    }
}
