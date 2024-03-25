<?php

namespace App\Models\Category;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Icon\Icon;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

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
}
