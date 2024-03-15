<?php

namespace App\Models;

use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\Transaction\Transaction;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function language(): HasOneThrough
    {
        return $this->hasOneThrough(Language::class, UserSetting::class, 'user_id', 'id', 'id', 'language_id');
    }

    public function currency(): HasOneThrough
    {
        return $this->hasOneThrough(Currency::class,
            UserSetting::class,
            'user_id',
            'id',
            'id',
            'main_currency_id'
        );
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'user_id', 'id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Account::class);
    }

    public static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
