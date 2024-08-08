<?php

namespace App\Models\Language;

use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Language extends Model
{
    use HasFactory;

    protected $guarded = ['*'];

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSettings::class, 'language_id', 'id');
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(UserSettings::class, User::class, 'language_id', 'user_id', 'id', 'id');
    }
}
