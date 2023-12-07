<?php

namespace App\Models\Language;

use App\Models\User;
use App\Models\UserSetting;
use Database\Factories\Language\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Language extends Model
{
    use HasFactory;

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'language_id', 'id');
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(UserSetting::class, User::class, 'language_id', 'user_id', 'id', 'id');
    }
}

