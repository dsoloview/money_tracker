<?php

namespace App\Models\Transfer;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Models\Account\Account;
use App\Models\Currency\Currency;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Transfer extends Model
{
    use HasFactory;
    use Filterable;
    use Sortable;

    protected $fillable = [
        'account_from_id',
        'account_to_id',
        'amount_from',
        'amount_to',
        'comment',
        'date',
    ];

    public function accountFrom(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_from_id');
    }

    public function accountTo(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_to_id');
    }

    public function currencyFrom(): HasOneThrough
    {
        return $this->hasOneThrough(
            Currency::class,
            Account::class,
            'id',
            'id',
            'account_from_id',
            'currency_id');
    }

    public function currencyTo(): HasOneThrough
    {
        return $this->hasOneThrough(
            Currency::class,
            Account::class,
            'id',
            'id',
            'account_to_id',
            'currency_id');
    }

    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }
}
