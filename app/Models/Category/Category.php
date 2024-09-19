<?php

namespace App\Models\Category;

use Abbasudo\Purity\Traits\Filterable;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Icon\Icon;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Traits\ElasticSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use HasFactory;
    use ElasticSearchable;
    use Searchable;
    use Filterable;

    public $filterFields = [
        'id',
        'name',
        'description',
    ];

    protected $fillable = [
        'user_id',
        'parent_category_id',
        'name',
        'type',
        'icon_id',
        'description',
    ];

    protected $casts = [
        'type' => CategoryTransactionType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'categories_transactions', 'category_id', 'transaction_id');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'id');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Icon::class, 'icon_id', 'id');
    }

    public function toSearchableArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public function toElasticSearchableArray(): array
    {
        return $this->toSearchableArray();
    }
}
