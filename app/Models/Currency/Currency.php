<?php

namespace App\Models\Currency;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Currency extends Model
{
    use HasFactory;

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'main_currency_id', 'id');
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(UserSetting::class, User::class, 'main_currency_id', 'user_id', 'id', 'id');
    }
}
