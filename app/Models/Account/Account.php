<?php

namespace App\Models\Account;

use App\Models\Currency\Currency;
use App\Models\Transaction\Transaction;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Services\Currency\CurrencyConverterService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Account extends Model
{
    use HasFactory;

    protected $with = ['currency', 'user'];

    protected $fillable = [
        'user_id',
        'currency_id',
        'name',
        'balance',
        'bank',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transferTo(): HasMany
    {
        return $this->hasMany(Transfer::class, 'account_to_id');
    }

    public function transferFrom(): HasMany
    {
        return $this->hasMany(Transfer::class, 'account_from_id');
    }

    public function getTransfersAttribute(): Collection
    {
        return $this->transferTo->merge($this->transferFrom);
    }

    public function balance(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }

    public function userCurrencyBalance(): Attribute
    {
        $currencyConverterService = new CurrencyConverterService();
        $user = $this->user;
        $accountCurrency = $this->currency->code;
        $accountBalance = $this->balance;
        $userMainCurrency = $user->currency->code;

        if ($userMainCurrency !== $accountCurrency) {
            $accountBalance = $currencyConverterService->convert(
                $accountBalance,
                $accountCurrency,
                $userMainCurrency
            );
        }

        return Attribute::make(
            get: fn() => $accountBalance,
        );
    }
}
