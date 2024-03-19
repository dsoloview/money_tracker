<?php

namespace App\Models\Transaction;

use Abbasudo\Purity\Filters\Resolve;
use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Services\Currency\CurrencyConverterService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    use HasFactory;
    use Filterable;
    use Sortable;

    protected $fillable = [
        'account_id',
        'amount',
        'type',
        'comment',
        'date',
        'account_balance'
    ];

    protected $casts = [
        'type' => CategoryTransactionType::class,
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categories_transactions', 'transaction_id', 'category_id');
    }

    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }

    public function scopeFilter(Builder $query, array|null $params = null): Builder
    {
        $this->bootFilter();

        if (!isset($params)) {
            $params = request()->query('filters', []);
        }

        foreach ($params as $field => $value) {
            if ($field === 'amount') {
                if (is_array($value)) {
                    $value = array_map(fn($v) => $v * 100, $value);
                } else {
                    $value *= 100;
                }
            }
            app(Resolve::class)->apply($query, $field, $value);
        }

        return $query;
    }

    public function getUserCurrencyAmountAttribute(): float
    {
        $currencyConverterService = new CurrencyConverterService();
        $user = $this->account->user;
        $accountCurrency = $this->account->currency->code;
        $transactionAmount = $this->amount;
        $userMainCurrency = $user->currency->code;

        if ($userMainCurrency !== $accountCurrency) {
            $transactionAmount = $currencyConverterService->convert($transactionAmount, $accountCurrency,
                $userMainCurrency);
        }

        return $transactionAmount;
    }
}
