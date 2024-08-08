<?php

namespace App\Models\Currency;

use App\Models\Account\Account;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = ['*'];

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSettings::class, 'main_currency_id', 'id');
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(UserSettings::class, User::class, 'main_currency_id', 'user_id', 'id', 'id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
