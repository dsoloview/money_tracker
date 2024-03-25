<?php

namespace App\Models\Icon;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Icon extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'name',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function path(): Attribute
    {
        return new Attribute(
            function ($value) {
                return asset($value);
            }
        );
    }
}
